<?php

/**
 * IndexController test case.
 */

namespace Tests;

use \PHPUnit\Framework\TestCase;
use Config\Config;

class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        if (empty($_SERVER["SERVER_NAME"])) {
            $_SERVER["SERVER_NAME"] = 'phpunit';
        }

        new Config();
        parent::setUp();
    }

    public function __destruct()
    {

    }
}
