<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Database\Models\Character;
use App\Database\Models\Account;

$accountId = $argv[1];
$name = $argv[2];
$charset = $argv[3];

if (count($argv) != 4) {
    echo 'Missing parameter';
    die;
}

Database::get()->getEntityManager()->transactional(
    function ($entityManager) {
        global $accountId, $name, $charset;

        $account = Account::findById($accountId);
        if ($account === null) {
            echo "Account not found";
            die;
        }

        $character = new Character();
        $character->setName($name);
        $character->setAccount($account);
        $character->setCharset($charset);
        $character->save();

        $account->setCurrentCharacter($character);
        $account->save();

        echo 'Character with ID: ' . $character->getId() . " created";
    }
);
