<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once __DIR__ . '/bootstrap.php';
//require_once(__DIR__ . '/src/app/Server.php');

use App\Server;
use App\Autoloader;

Autoloader::register();

$options = array();

foreach($argv as $arg) {
    switch($arg) {
        case '--debug':
            $options['debug'] = true;
            break;
    }
}

$server = new Server($options);

try {
    $server->run();
} catch (Exception $e) {
    $server->stderr($e->getMessage());
}
