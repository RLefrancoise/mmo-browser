<?php

namespace App\Game\Data\Map;

class TileData {

    public function __construct($number, $tileset, $x, $y) {
        $this->tileset = $tileset;
    	$this->number = ($number - $this->tileset->firstgid + 1);
    	$this->x = $x;
    	$this->y = $y;
    }

    public function getProperties() {
        if(empty($this->tileset->tileproperties)) return array();

    	if( array_key_exists($this->number - 1, $this->tileset->tileproperties)) {
    		return $this->tileset->tileproperties[$this->number - 1];
    	} else {
    		return array();
    	}
    }
}
