<?php

namespace App\Game\Data;

use App\Server;
use App\Game\Data\Map\MapEventTile;
use App\Game\Data\Map\TilesetData;
use App\Game\Data\Map\TileData;

class MapData
{
    protected $path;
    protected $tilesets;
    protected $grid;

    protected $rawData;

    protected $properties;
    protected $width;
    protected $height;
    protected $tileWidth;
    protected $tileHeight;

    public function __construct($path)
    {
        $this->path = $path;
        $this->tilesets = array();
        $this->grid = array();

        $this->rawData = file_get_contents(SERVER_DIR . "/data/maps/{$path}/{$path}.json");
        $data = json_decode($this->rawData, true);

        $this->properties = $data['properties'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->tileWidth = $data['tilewidth'];
        $this->tileHeight = $data['tileheight'];

        //tilesets
        foreach ($data['tilesets'] as $tileset_data) {
            $this->tilesets[] = new TilesetData($tileset_data);
        }

        //grid
        for ($y = 0 ; $y < $this->height ; $y++) {
            $this->grid[] = array();
            for ($x = 0 ; $x < $this->width ; $x++) {
                $this->grid[$y][] = array();
            }
        }

        for ($t = 0 ; $t < $this->width * $this->height ; $t++) {
            //iterate through each tile layer
            for ($l = 0 ; $l < count($data['layers']); $l++) {
                $layer = $data['layers'][$l];

                if (!$layer['visible']) continue;
                if ($layer['type'] != "tilelayer") continue;

                $x = $t % $this->width;
                $y = ($t - $x) / $this->width;

                $this->grid[$y][$x][] = ($layer['data'][$t] != 0) 
                    ? new TileData($layer['data'][$t], $this->getTilesetOfTile($layer['data'][$t])['tileset'], $x, $y)
                    : null;
            }
        }

        //map events
        $this->events = array();
        for ($y = 0 ; $y < $this->height ; $y++) {
            $this->events[] = array();
        }

        for ($l = 0 ; $l < count($data['layers']); $l++) {
            $layer = $data['layers'][$l];

            if (!$layer['visible']) continue;
            if ($layer['type'] != "objectgroup" || ($layer['type'] == "objectgroup" && $layer['name'] != "events")) continue;

            $events = $layer['objects'];

            for ($e = 0 ; $e < count($events); $e++) {
                $ev = $events[$e];

                if ($ev['type'] != "event") continue;
                if (!$ev['visible']) continue;

                //get tiles this event in on
                $startx = $ev['x'] / $this->tileWidth;
                $starty = $ev['y'] / $this->tileHeight;
                $endx = ($ev['x'] + $ev['width']) / $this->tileWidth - 1;
                $endy = ($ev['y'] + $ev['height']) / $this->tileHeight - 1;

                for ($x = $startx ; $x <= $endx ; $x++) {
                    for ($y = $starty ; $y <= $endy ; $y++) {
                        $this->events[$y][$x] = new MapEventTile($this, $ev);
                    }
                }
            }
        }
    }

    public function toJSONArray()
    {
        return json_decode($this->rawData, true);
    }

    public function getEvents($x, $y)
    {
        if (isset($this->events[$y][$x])) {
            return $this->events[$y][$x]->eventPages;
        }
        else return false;
    }

    public function getTilesetOfTile($tile)
    {
        for ($ts = count($this->tilesets) - 1; $ts >= 0 ; $ts--) {
            if ($tile >= $this->tilesets[$ts]->firstgid) {
                return array(
                    'tileset'   =>  $this->tilesets[$ts],
                    'index'     =>  $ts,
                );
            }
        }

        return false;
    }

    public function isWalkable($x, $y)
    {
        $walkable = true;

        for ($k = 0 ; $k < count($this->grid[$y][$x]); $k++) {
            $tile = $this->grid[$y][$x][$k];

            if ($tile === null) continue;

            //if tile is not walkable
            if (isset($tile->getProperties()['passable']) && $tile->getProperties()['passable'] == "0") {
                return false;
            }
        }

        return $walkable;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getName()
    {
        return $this->properties['name'];
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public static function loadMapsData(Server $server)
    {
        $data = array();

        $server->stdout("--------------------");
        $server->stdout("Loading maps data...");

        $a_maps = scandir(SERVER_DIR . '/data/maps');

        foreach ($a_maps as $map) {
            if ($map == '.' || $map == '..') continue;

            if (is_dir(SERVER_DIR . '/data/maps/' . $map)) {
                $data[$map] = new MapData($map);
                $server->stdout("\t*{$map} ({$data[$map]->getName()} - {$data[$map]->getWidth()}x{$data[$map]->getHeight()})");
            }
        }

        $server->stdout("Maps data loaded.");
        $server->stdout("--------------------");

        return $data;
    }
}
