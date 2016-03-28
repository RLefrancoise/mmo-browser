<?php

namespace App\Helpers;

use WebSocketUser;
use App\Server;
use App\Database\Models\WorldZone;
use App\Database\Models\Character;
use App\Game\Data\Map\MapEvent;
use App\Network\Packets\Packet;
use App\Network\Packets\Server\Map\SM_MAP_SELF_SPAWN;
use App\Network\Packets\Server\Map\SM_MAP_PLAYER_MOVE;
use App\Network\Packets\Server\Map\SM_MAP_SPAWN_PLAYER;
use App\Network\Packets\Server\Map\SM_MAP_PLAYER_EXITS_MAP;

class MapHelper {
    protected $server;

    public function __construct(Server $server) {
        $this->server = $server;
    }

    public function getMapData($mapId) {
        return $this->server->getGameData('map', $mapId);
    }

    public function moveCharacter(WebSocketUser $user, $x, $y, $direction) {
        $account = $this->server->getAccountFromUser($user);
        if($account) {
            $ch = $account->getCurrentCharacter();
            //We get the character position
            $wp = $ch->getWorldPosition();
            //We get map data for the world zone the character is in
            $mapData = $this->server->getGameData('map', array('name' => $wp->getWorldZone()->getName()));
            //Check if the tile is walkable, if not, we do nothing
            if($mapData->isWalkable($x, $y)) {
                $wp->setX($x);
                $wp->setY($y);
                $wp->setDirection($direction);
                $wp->save();
                $ch->setWorldPosition($wp);
                $ch->save();

                $sm_map_player_move = Packet::create(array_merge(array('type'  =>  SM_MAP_PLAYER_MOVE::TYPE), $ch->toJSONArray()));
                $sm_map_player_move->doAction($this->server, $user);

                //check events
                $events = $mapData->getEvents($x, $y);
                if($events) {
                    for($i = count($events) - 1 ; $i >= 0 ; $i--) {
                        $event = $events[$i];
                        $event->execute($this->server, $user);
                    }
                }    
            }
        }
    }

    public function selfSpawn(WebSocketUser $user) {
        //self spawn
        $sm_map_self_spawn = Packet::create(array(
            'type'  =>  SM_MAP_SELF_SPAWN::TYPE,
        ));
        $sm_map_self_spawn->doAction($this->server, $user);

        //send to other players on same map that a new player has arrived
        $sm_map_spawn_player = Packet::create(array(
            'type'  =>  SM_MAP_SPAWN_PLAYER::TYPE,
        ));
        $sm_map_spawn_player->doAction($this->server, $user);
    }

    public function warpUserToMap(WebSocketUser $user, $warpMap, $warpX, $warpY, $warpDirection) {
        $ch = $this->server->getAccountFromUser($user)->getCurrentCharacter();

        //first, exit the current map
        $sm_map_player_exits_map = Packet::create(array(
            'type'  =>  SM_MAP_PLAYER_EXITS_MAP::TYPE,
        ));
        $sm_map_player_exits_map->doAction($this->server, $user);

        //change map position
        $wp = $ch->getWorldPosition();
        $wp->setX($warpX);
        $wp->setY($warpY);
        $wp->setDirection($warpDirection);

        $wz = WorldZone::findOneBy(array(
            'name'  =>  $warpMap,
        ));

        if(!$wz) {
            $this->server->stderr("MapHelper::warpUserToMap : failed to get WorldZone with name = {$warpMap}");
            return;
        }

        $wp->setWorldZone($wz);
        $wp->save();
        $ch->setWorldPosition($wp);
        $ch->save();

        //and respawn player
        $this->selfSpawn($user);
    }
}
