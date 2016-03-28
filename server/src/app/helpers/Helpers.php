<?php
namespace App\Helpers;

use App\Server;

class Helpers {

    public static $MAP_HELPER;
    public static $ACCOUNT_HELPER;

    private function __construct() {

    }

    public static function initHelpers(Server $server) {
        self::$MAP_HELPER = new MapHelper($server);
        self::$ACCOUNT_HELPER = new AccountHelper($server);
    }
}
