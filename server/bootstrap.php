<?php

require_once __DIR__ . '/src/app/database/Database.php';

use App\Database\Database;

define('SERVER_DIR', __DIR__);

Database::get();