<?php
// cli-config.php
require_once __DIR__ . "/bootstrap.php";

use App\Database\Database;
use \Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(Database::get()->getEntityManager());