<?php

require_once __DIR__ . '/../bootstrap.php';

$accountId = $argv[1];

if (empty($accountId)) {
    die("No accountId");
}

$account = $entityManager->find("Account", $accountId);
if ($account === null) {
    die("No account with ID $accountId");
}

$characters = $account->getCharacters();

foreach ($characters as $character) {
    echo $character . PHP_EOL;
}
