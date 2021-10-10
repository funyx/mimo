<?php

use Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = require_once __DIR__.'/../core/app.php';

$_SERVER['app']->run();
