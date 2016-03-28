<?php

require_once(__DIR__ . '/src/app/database/Database.php');

use App\Database\Database;

/*use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once(__DIR__ . '/vendor/autoload.php');

$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_pgsql',
    'user'     => 'postgres',
    'password' => 'ThOfSh0!',
    'dbname'   => 'exitium_rpg',
);

$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);
$entityManager = EntityManager::create($dbParams, $config);*/

define('SERVER_DIR', __DIR__);

Database::get();
