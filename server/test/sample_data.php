<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Database\Models\Charset;
use App\Database\Models\Account;
use App\Database\Models\WorldMap;
use App\Database\Models\Character;
use App\Database\Models\WorldZone;
use App\Database\Models\WorldPosition;

Database::get()->getEntityManager()->transactional(function($entityManager) {

    //charsets
    $charsets = array(
        array(
            'name'  =>  'Yuki Kazuki',
            'file'  =>  'yuki.png',
            'locked'    =>  false,
        ),
        array(
            'name'  =>  'Tsukasa Watanabe',
            'file'  =>  'tsukasa.png',
            'locked'    =>  false,
        ),
    );

    foreach($charsets as $charset) {
        $char = Charset::findOneBy(array('name' =>  $charset['name']));
        if($char === null) {
            $char = new Charset();
            $char->setName($charset['name']);
            $char->setFile($charset['file']);
            $char->setIsLocked($charset['locked']);
            if(isset($charset['owner'])) $char->setOwner(Account::findOneBy(array('login'   =>  $charset['owner'])));
            $char->save();
            echo "Saved Charset {$char->getName()}" .PHP_EOL;
        }
    }

    //accounts
    $accounts = array(
        array(
            'login' =>  'Luka',
            'pass'  =>  'blabla',
            'mail'  =>  'test@test.com',
            'characters'    =>  array(
                array(
                    'name'  =>  'Yuki Kazuki',
                    'charset'   =>  'Yuki Kazuki',
                    'position'  =>  array(
                        'x' =>  2,
                        'y' =>  3,
                        'direction' =>  Character::DIRECTION_DOWN,
                        'zone'      =>  'map1',
                    )
                )
            )
        ),
        array(
            'login' =>  'Roxas',
            'pass'  =>  'blabla',
            'mail'  =>  'test@example.com',
            'characters'    =>  array(
                array(
                    'name'  =>  'Tsukasa Watanabe',
                    'charset'   =>  'Tsukasa Watanabe',
                    'position'  =>  array(
                        'x' =>  3,
                        'y' =>  3,
                        'direction' =>  Character::DIRECTION_RIGHT,
                        'zone'      =>  'map1',
                    )
                )
            )
        )
    );

    foreach($accounts as $account) {
        if(Account::findOneBy(array('login' =>  $account['login'])) === null) {
            $a = new Account();
            $a->setLogin($account['login']);
            $a->setPassword(md5($account['pass']));
            $a->setMail($account['mail']);

            foreach($account['characters'] as $character) {
                if(Character::findOneBy(array('name'    =>  $character['name'])) === null) {
                    $c = new Character();
                    $c->setName($character['name']);
                    $c->setCharset(Character::findOneBy(array('name'    =>  $character['charset'])));
                    $c->setAccount($a);

                    $wp = new WorldPosition();
                    $wp->setX($character['position']['x']);
                    $wp->setY($character['position']['y']);
                    $wp->setDirection($character['position']['direction']);
                    $wp->setWorldZone(WorldZone::findOneBy(array('name' => $character['position']['zone'])));
                    $wp->save();

                    $c->setWorldPosition($wp);
                    $c->save();

                    $a->setCurrentCharacter($c);
                    echo "Saved Character {$c->getName()}" . PHP_EOL;
                }

            }

            $a->save();
            echo "Saved Account {$a->getLogin()}" . PHP_EOL;
        }
    }



    echo "Done" . PHP_EOL;
});
