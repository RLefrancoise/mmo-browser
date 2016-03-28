<?php

namespace App\Network\Packets\Server\Chat;

use App\Network\Packets\Server\ServerPacket;
use App\Server;
use WebSocketUser;

abstract class ServerChatPacket extends ServerPacket {

    const MSG_COLOR_DEFAULT = 'rgb(255,255,255)';
    const MSG_COLOR_SERVER = 'rgb(255,128,0)';

    public function doAction(Server $server, WebSocketUser $user) {

    }
}
