<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Database\Models\WorldMap;
use App\Database\Models\WorldZone;

Database::get()->getEntityManager()->transactional(function($entityManager) {

    $maps = array(
        'map1'  =>  array(
            'map1',
        ),
        'map3'  =>  array(
            'map3',
        ),
    );

    foreach($maps as $mapName => $mapZones) {
        $map = WorldMap::findByName($mapName);
        if($map === null) {
            $map = new Worldmap();
            $map->setName($mapName);
            $map->save();
            echo "Save map (ID: {$map->getId()}, Name: {$map->getName()})" . PHP_EOL;
        }

        foreach($mapZones as $zoneName) {
            $zone = WorldZone::findOneBy(array(
                'name'  =>  $zoneName,
            ));

            if($zone === null) {
                $zone = new WorldZone();
                $zone->setName($zoneName);
                $zone->setWorldMap($map);
                $zone->save();
                echo "Save zone (ID: {$zone->getId()}, Name: {$zone->getName()})" . PHP_EOL;
            }
        }
    }

});
