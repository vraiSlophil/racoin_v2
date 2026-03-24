<?php

declare(strict_types=1);

namespace App\Db;

use Illuminate\Database\Capsule\Manager as DB;

class Connection
{
    public static function createConn(): void
    {
        $capsule = new DB();
        $capsule->addConnection(parse_ini_file(dirname(__DIR__, 2) . '/config/config.ini'));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
