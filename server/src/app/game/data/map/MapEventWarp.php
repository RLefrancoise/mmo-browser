<?php

namespace App\Game\Data\Map;

use App\Helpers\Helpers;
use App\Server;
use WebSocketUser;

/**
 * A map event
 */
class MapEventWarp extends MapEvent
{
    protected $warpMap;
    protected $warpX;
    protected $warpY;
    protected $warpDirection;

    public function __construct($mapRef, $data)
    {
        parent::__construct($mapRef, $data);

        $this->direction = (isset($data['direction'])) ? $data['direction'] : null;
        if ($data['data']) {
            $this->warpMap = $data['data']['map'];
            $this->warpX = $data['data']['x'];
            $this->warpY = $data['data']['y'];
            $this->warpDirection = isset($data['data']['direction']) ? $data['data']['direction'] : null;
        }
    }

    public function execute(Server $server, WebSocketUser $user)
    {
        if ($this->conditionsMet($server, $user)) {
            Helpers::$MAP_HELPER->warpUserToMap(
                $user,
                $this->warpMap,
                $this->warpX,
                $this->warpY,
                (!is_null($this->warpDirection))
                    ? $this->warpDirection
                    : $server->getAccountFromUser($user)->getCurrentCharacter()->getWorldPosition()->getDirection()
            );
        }
    }

    private function conditionsMet(Server $server, WebSocketUser $user)
    {
        switch($this->trigger) {
            case MapEvent::TRIGGER_PLAYER_CONTACT:
                $wp = $server->getAccountFromUser($user)->getCurrentCharacter()->getWorldPosition();
                if (is_null($this->direction)) return true;
                else return $wp->getDirection() == $this->direction;
                /*if($wp->getX() == $this->warpX && $wp->getY() == $this->getY()) {
                    if(empty($this->warpDirection)) return true;
                    else return $wp->getDirection() == $this->direction;
                }*/
                break;
        }

        return false;
    }
}
