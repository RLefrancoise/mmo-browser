<?php

namespace App\Network\Packets\Server\Map;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;

class SM_MAP_PLAYER_MOVE extends ServerMapPacket {

    const TYPE = 'sm_map_player_move';

    public function __construct($data) {
        parent::__construct($data);
    }

    public function getX() {
        return $this->data['x'];
    }

    public function getY() {
        return $this->data['y'];
    }

    public function getDirection() {
        return $this->data['direction'];
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);

        $this->broadcast($server, $user, Packet::BROADCAST_SAME_LOCATION | Packet::BROADCAST_EXLUDE_SELF);
    }
}
