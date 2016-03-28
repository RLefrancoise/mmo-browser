<?php

namespace App\Network\Packets\Server\Map;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;

class SM_MAP_SPAWN_PLAYER extends ServerMapPacket {

    const TYPE = 'sm_map_spawn_player';

    public static $dataStructure = array(
        'get'  =>  array(
            'playerdata',
        ),
        'set' =>  array(
        ),
    );

    public function __construct($data) {
        parent::__construct($data);
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);

        $account = $server->getAccountFromUser($user);
        $this->data['playerdata'] = $account->getCurrentCharacter()->toJSONArray();

        $this->broadcast($server, $user, Packet::BROADCAST_SAME_LOCATION | Packet::BROADCAST_EXLUDE_SELF);
    }
}
