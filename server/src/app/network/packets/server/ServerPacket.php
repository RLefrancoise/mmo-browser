<?php

namespace App\Network\Packets\Server;

use App\Network\Packets\Packet;
use App\Server;
use WebSocketUser;

abstract class ServerPacket extends Packet {

    public function doAction(Server $server, WebSocketUser $user) {
        $account = $server->getAccountFromUser($user);
        if($account == null) {
            $sm_login_error = new SM_LOGIN_ERROR(array(
                'error'  =>  'You are not connected !',
            ));
            $sm_login_error->send($server, $user);
        }
    }
}
