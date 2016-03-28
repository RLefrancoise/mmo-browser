<?php

namespace App\Network\Packets\Server\Login;

use App\Network\Packets\Server\ServerPacket;
use App\Server;
use WebSocketUser;

abstract class LoginPacket extends ServerPacket {

    public function doAction(Server $server, WebSocketUser $user, $account = null) {
    }
}
