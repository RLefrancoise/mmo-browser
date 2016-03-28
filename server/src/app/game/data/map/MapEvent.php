<?php

namespace App\Game\Data\Map;
use App\Database\Models\Character;
use App\Helpers\Helpers;
use App\Helpers\MapHelper;
use App\Server;
use WebSocketUser;

/**
* A map event
*/
abstract class MapEvent {

    const EVENT_TYPE_WARP           = 'warp';
    const EVENT_TYPE_SCRIPT         = 'script';

    const TRIGGER_PLAYER_CONTACT    = 'player_contact';
    const TRIGGER_KEYPRESS          = 'keypress';
    const TRIGGER_AUTO              = 'auto';

    protected $mapRef;
    protected $type;
    protected $trigger;
    protected $conditions;

    public function __construct($mapRef, $data) {
        $this->mapRef = $mapRef;
    	$this->type = $data['type']; //event type (warp, script, ...)
    	$this->trigger = $data['trigger']; //event trigger (keypress, player_contact, auto, ...)
    	if(!empty($data['conditions'])) $this->conditions = $data['conditions']; //event existence conditions (switch activated, ...)

    	/*switch($this->type) {
    		case MapEvent::EVENT_TYPE_WARP:
    			$this->_initFromWarpType($data);
    			break;
    		case MapEvent::EVENT_TYPE_SCRIPT:
    			$this->_initFromScriptType($data);
    			break;
    	}*/
    }

    public abstract function execute(Server $server, WebSocketUser $user);

    public function getTrigger() {
        return $this->trigger;
    }
}
