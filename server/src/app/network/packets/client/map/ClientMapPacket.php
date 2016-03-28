<?php

namespace App\Network\Packets\Client\Map;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;

abstract class ClientMapPacket extends Packet {

    public function doAction(Server $server, WebSocketUser $user) {

    }
}
