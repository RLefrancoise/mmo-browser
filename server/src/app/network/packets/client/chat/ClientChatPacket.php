<?php

namespace App\Network\Packets\Client\Chat;

use App\Network\Packets\Client\ClientPacket;
use App\Server;
use WebSocketUser;

abstract class ClientChatPacket extends ClientPacket {
    public function doAction(Server $server, WebSocketUser $user) {

    }
}
