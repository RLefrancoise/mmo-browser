<?php

namespace App\Helpers;

use DateTime;
use WebSocketUser;
use App\Server;
use App\Database\Models\Account;
use App\Network\Packets\Packet;
use App\Network\Packets\Server\Chat\ServerChatPacket;
use App\Network\Packets\Server\Login\SM_LOGIN_ERROR;
use App\Network\Packets\Server\Map\SM_MAP_PLAYER_EXITS_MAP;
use App\Network\Packets\Server\Utils\SM_SERVER_MESSAGE;

class AccountHelper
{
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function login(WebSocketUser $user, $login, $password)
    {
        $account = Account::getFromLoginPassword($login, $password);

        if ($account !== null) {
            $account->login($user->id);
            $this->server->addConnectedAccount($account);
            $this->server->stdout("User {$account->getLogin()} has logged in.");

            //spawn character
            Helpers::$MAP_HELPER->selfSpawn($user);

            $sm_server_message = Packet::create(
                array(
                    'type'  =>  SM_SERVER_MESSAGE::TYPE,
                    'msg'   =>   "Welcome to Exitium. Server time : " . (new Datetime())->format('d/m/Y H:i:s'),
                    'color' =>  ServerChatPacket::MSG_COLOR_SERVER,
                )
            );
            $sm_server_message->doAction($this->server, $user);

            /*$sm_map_self_spawn = Packet::create(array(
                'type'  =>  SM_MAP_SELF_SPAWN::TYPE,
            ));
            $sm_map_self_spawn->doAction($this->server, $user);*/
        } else {
            $sm_login_error = Packet::create(
                array(
                    'type'  =>  SM_LOGIN_ERROR::TYPE,
                    'error'  =>  'This account does not exist !',
                )
            );
            $sm_login_error->send($this->server, $user);
        }
    }

    public function logout(WebSocketUser $user)
    {
        $account = $this->server->getAccountFromUser($user);
        if ($account) {
            $account->logout();

            $sm_map_player_exists_map = Packet::create(
                array(
                    'type'  =>  SM_MAP_PLAYER_EXITS_MAP::TYPE,
                )
            );
            $sm_map_player_exists_map->doAction($this->server, $user);

            $this->server->removeConnectedAccount($account);
            $this->server->stdout("User  {$account->getLogin()} has logged out.");
        }
    }
}
