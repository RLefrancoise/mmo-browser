<?php
// cli-config.php
require_once __DIR__ . "/bootstrap.php";

use App\Database\Database;

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(Database::get()->getEntityManager());
