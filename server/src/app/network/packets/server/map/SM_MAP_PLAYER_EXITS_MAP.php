<?php

namespace App\Network\Packets\Server\Map;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;

class SM_MAP_PLAYER_EXITS_MAP extends ServerMapPacket {

    const TYPE = 'sm_map_player_exits_map';

    public function __construct($data) {
        parent::__construct($data);
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);
        $account = $server->getAccountFromUser($user);

        if($account) {
            $this->data['id']   = $account->getCurrentCharacter()->getId();
            $this->broadcast($server, $user, Packet::BROADCAST_SAME_LOCATION | Packet::BROADCAST_EXLUDE_SELF);
        }
    }
}
