<?php

use Config\Config;
use Framework\Box;

require 'App/Config/Config.php';
new Config();

return [
    "paths" => [
        "migrations" => "data/migrations",
        "seeds"      => "data/seeds",
    ],
    "environments" => [
        "default_migration_table" => "db_versions",
        "default_database"        => "local",
        "local"                   => [
            "adapter" => Box::$env['database']['type'],
            "host"    => Box::$env['database']['hostname'],
            "name"    => Box::$env['database']['database'],
            "user"    => Box::$env['database']['username'],
            "pass"    => Box::$env['database']['password'],
            "port"    => Box::$env['database']['port'],
        ],
    ],
    "version_order"              => "creation",
];
