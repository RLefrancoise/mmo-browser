<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap.php';

use App\Database\Database;
use App\Database\Models\Account;

$login = $argv[1];
$pass = $argv[2];
$mail = $argv[3];

if (empty($login) || empty($pass)) {
    echo "Missing parameter.";
    die;
}

Database::get()->getEntityManager()->transactional(
    function ($entityManager) {
        global $login, $pass, $mail;

        $account = new Account();
        $account->setLogin($login);
        $account->setPassword(md5($pass));
        $account->setMail($mail);

        $account->save();

        echo "Created Account with ID " . $account->getId() . "\n";
    }
);
