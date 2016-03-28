<?php

namespace App\Network\Packets\Server\Map;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;

abstract class ServerMapPacket extends Packet {

    public function doAction(Server $server, WebSocketUser $user) {
        
    }
}
