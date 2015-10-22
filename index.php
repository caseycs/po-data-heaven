<?php
//for built-in php server
if (is_file(__DIR__ . '/public' . $_SERVER['REQUEST_URI'])) {
    return false;
}

use Dotenv\Dotenv;
use PODataHeaven\PoDataHeavenApplication;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new PoDataHeavenApplication();
$app->run();
