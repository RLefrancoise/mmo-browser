<?php

namespace App\Game\Data\Map;

class TilesetData
{
    public $name;
    public $tileWidth;
    public $tileHeight;
    public $tileproperties;
    public $firstgid;
    
    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->tileWidth = $data['tilewidth'];
        $this->tileHeight = $data['tileheight'];
        $this->tileproperties = !empty($data['tileproperties']) ? $data['tileproperties'] : array();
        $this->firstgid = $data['firstgid'];
    }
}
