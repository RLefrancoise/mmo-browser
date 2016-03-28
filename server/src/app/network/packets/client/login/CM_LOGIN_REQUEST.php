<?php

namespace App\Network\Packets\Client\Login;
use LogicException;
use App\Helpers\Helpers;
use App\Network\Packets\Packet;
use App\Server;
use WebSocketUser;
use App\Database\Models\Account;
use App\Database\Database;

use App\Network\Packets\Client\ClientPacket;
use App\Network\Packets\Server\Login\SM_LOGIN_ERROR;

class CM_LOGIN_REQUEST extends ClientPacket {

    const TYPE = 'cm_login_request';

    public static $dataStructure = array(
        'get'  =>  array(
            'login',
            'password',
        ),
        'set' =>  array(
            'login',
            'password',
        ),
    );

    public function __construct($data) {
        parent::__construct($data);

        if(empty($this->data['login']) || empty($this->data['password'])) {
            throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - CM_LOGIN_REQUEST : invalid data.');
        }
    }

    public function getType() {
        return CM_LOGIN_REQUEST::TYPE;
    }

    public function getLogin() : string {
        return $this->data['login'];
    }

    public function getPassword() : string {
        return $this->data['password'];
    }

    public function doAction(Server $server, WebSocketUser $user) {
        Helpers::$ACCOUNT_HELPER->login($user, $this->getLogin(), md5($this->getPassword()));
        /*$account = Account::getFromLoginPassword($this->getLogin(), md5($this->getPassword()));

        if($account !== null) {
            $account->login($user->id);
            $server->addConnectedAccount($account);
            $server->stdout("User {$account->getLogin()} has logged in.");
        } else {
            $sm_login_error = new SM_LOGIN_ERROR(array(
                'error'  =>  'This account does not exist !',
            ));
            $sm_login_error->send($server, $user);
        }*/
    }
}
