<?php

namespace App\Network\Packets\Server\Map;

use App\Server;
use WebSocketUser;
use App\Network\Packets\Server\Map\ServerMapPacket;

class SM_MAP_SELF_SPAWN extends ServerMapPacket
{
    const TYPE = 'sm_map_self_spawn';

    public static $dataStructure = array(
        'get'  =>  array(
            'mapdata',
            'playerdata',
        ),
        'set' =>  array(
        ),
    );

    public function __construct($data = array())
    {
        parent::__construct($data);
    }

    public function doAction(Server $server, WebSocketUser $user)
    {
        parent::doAction($server, $user);

        $account = $server->getAccountFromUser($user);

        $this->data['mapdata'] = $server->getGameData(
            'map',
            array(
                'name'  =>  $account->getCurrentCharacter()->getWorldPosition()->getWorldZone()->getName(),
            )
        )->toJSONArray();

        $this->data['playerdata'] = $account->getCurrentCharacter()->toJSONArray();

        $this->data['players'] = array();
        $connectedAccounts = $server->getConnectedAccounts();
        foreach ($connectedAccounts as $ac) {
            if ($ac->getId() == $account->getId()) continue;
            if ($ac->getCurrentCharacter()->isInSameLocation($account->getCurrentCharacter())) {
                $this->data['players'][] = $ac->getCurrentCharacter()->toJSONArray();
            }
        }

        $this->send($server, $user);
    }
}
