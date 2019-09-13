<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Models\Character;
use App\Database\Database;
use App\Database\Models\WorldZone;
use App\Database\Models\WorldPosition;

Database::get()->getEntityManager()->transactional(
    function ($entityManager) {
        global $argv;

        $ch = Character::findOneBy(
            array('name'  =>  $argv[1],)
        );

        if ($ch !== null) {

            $wz = WorldZone::findOneBy(
                array('name'  =>  $argv[4],)
            );

            if ($wz) {
                $wp = $ch->getWorldPosition();
                if (!$wp) {
                    $wp = new WorldPosition();
                }

                $wp->setX($argv[2]);
                $wp->setY($argv[3]);
                $wp->setDirection(Character::DIRECTION_DOWN);
                $wp->setWorldZone($wz);
                $wp->save();

                $ch->setWorldPosition($wp);
                $ch->save();
            } else {
                echo "No WorldZone found with name {$argv[4]}";
            }
        } else {
            echo "No Character found with name {$argv[1]}";
        }
    }
);