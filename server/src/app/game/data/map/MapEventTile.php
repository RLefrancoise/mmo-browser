<?php

namespace App\Game\Data\Map;

use App\Game\Data\Map\MapEvent;

/**
 * A map tile with all the events for this tile
 */
class MapEventTile
{
    public function __construct($mapRef, $evData)
    {
        $this->mapName = $mapRef->getPath();
        $this->name = $evData['name'];
        $this->eventPages = array();

        //load event data from specified file
        if ($evData['properties']['file']) {
            $file = file_get_contents(SERVER_DIR . "/data/maps/{$this->mapName}/events/{$evData['properties']['file']}.json");
            if ($file === false) {
                throw new LogicException("MapEventTile: can't load " . SERVER_DIR . "/data/maps/{$this->mapName}/events/{$evData['properties']['file']}.json");
            }

            $data = json_decode($file, true);

            $pages = $data['events'];

            foreach ($pages as $ev) {
                //check if required fields are specified
                if (!$ev['type']) continue;
                if (!$ev['trigger']) continue;

                switch($ev['type']) {
                    case MapEvent::EVENT_TYPE_WARP:
                        $this->eventPages[] = new MapEventWarp($mapRef, $ev);
                        break;
                }
            }
        }
    }
}
