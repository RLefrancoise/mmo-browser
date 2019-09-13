<?php

/**
 * Server.php
 * 
 * @package App
 * 
 * @author Renaud Lefrançoise <renaud.lefrancoise@gmail.com>
 */
namespace App;

use DateTime;
use WebSocketUser;
use LogicException;
use App\Helpers\Helpers;
use App\Network\Packets\Packet;
use Doctrine\Common\Collections\ArrayCollection;
use WebSocketServer;
use App\Database\Database;
use App\Game\Data\MapData;
use App\Database\Models\Account;
use Doctrine\DBAL\Query\QueryException;

require_once __DIR__ . '/../../libs/websockets/websockets.php';

class Server extends WebSocketServer
{

    protected $options = array();

    protected $connectedAccounts = null;

    protected $gameData;

    protected $stdout_log_handle;
    protected $stderr_log_handle;

    public function __construct($options)
    {
        parent::__construct('localhost', 8080);

        $this->options = $options;
        $this->connectedAccounts = new ArrayCollection();

        /*$this->stdout_log_handle = fopen(SERVER_DIR . '/stdout_log.txt', 'w+');
        $this->stderr_log_handle = fopen(SERVER_DIR . '/stderr_log.txt', 'w+');*/

        //Helpers
        Helpers::initHelpers($this);

        //Game data
        $this->loadGameData();

        try {
            $this->stdout("Log out all players...");
            Account::logoutAll();
            $this->stdout("...All players logged out.");
        } catch(QueryException $e) {
            $this->stderr($e->getMessage());
        }

        //$this->isRunning = true;
    }

    public function __destruct()
    {
        //parent::__destruct();
        echo "Destroy server...\n";
        /*fclose($this->stdout_log_handle);
        fclose($this->stderr_log_handle);*/
    }

    public function stdout($message)
    {
        if(!empty($this->options['debug'])) parent::stdout($message);
    }

    public function stderr($message)
    {
        parent::stderr($message);
        file_put_contents(SERVER_DIR . '/stderr.txt', $message);
    }

    private function loadGameData()
    {
        $this->gameData = array();
        $this->gameData['maps'] = MapData::loadMapsData($this);
    }

    public function getGameData($dataType, array $params)
    {
        switch($dataType) {
            case 'map':
                $mapName = $params['name'];
                $mapsData = $this->gameData['maps'];
                foreach($mapsData as $mapData) {
                    if(strcmp($mapData->getName(), $mapName) == 0) return $mapData;
                }
                break;
        }

        $s_params = print_r($params, true);
        throw new LogicException("Server:getGameData() : Failed to get game data with type={$dataType} and params={$s_params}");
    }

    protected function process($user,$message)
    {
        try {
            Database::get()->getEntityManager()->beginTransaction();

            $data = json_decode($message, true);
            //if($this->options['debug']) $this->stdout(print_r($data, true));

            $packet = Packet::create($data);
            $packet->doAction($this, $user);

            Database::get()->getEntityManager()->commit();

        } catch(Exception $e) {
            $this->stderr((new Datetime())->format('d/m/Y H:i:s') . $e->getMessage());
            $this->stderr("Packet received :" . print_r($data, true));
            Database::get()->getEntityManager()->rollback();
        }
    }

    protected function connected($user)
    {
    }

    protected function closed($user)
    {
        Helpers::$ACCOUNT_HELPER->logout($user);
    }

    public function send($user, $msg)
    {
        parent::send($user, $msg);
    }

    public function broadcast(WebSocketUser $user, $data, $flags = null)
    {
        $account = $this->getAccountFromUser($user);
        $toSend = array();

        foreach ($this->users as $u) {
            $ac = $this->getAccountFromUser($u);
            if ($ac == null) continue;

            if ($flags & Packet::BROADCAST_SAME_LOCATION) {
                if ($account->getCurrentCharacter()->isInSameLocation($ac->getCurrentCharacter())) $toSend[] = $u;
            }
        }

        if ($flags & Packet::BROADCAST_EXLUDE_SELF) {
            for ($i = 0 ; $i < count($toSend); $i++) {
                if ($this->getAccountFromUser($toSend[$i])->getCurrentCharacter()->getId() == $account->getCurrentCharacter()->getId()) {
                    unset($toSend[$i]);
                }
            }
        }

        //$toSend = array_unique($toSend);

        foreach ($toSend as $u) {
            $this->send($u, $data);
        }
    }

    public function addConnectedAccount(Account $account) : self
    {
        $this->connectedAccounts->set($account->getId(), $account);
        return $this;
    }

    public function getConnectedAccounts()
    {
        return $this->connectedAccounts;
    }

    public function getAccountFromUser(WebSocketUser $user)
    {
        foreach ($this->connectedAccounts as $account) {
            if ($account->getConnectionToken() == $user->id) return $account;
        }

        return null;
    }

    public function removeConnectedAccount(Account $account)
    {
        $this->connectedAccounts->remove($account->getId(), $account);
    }
}
