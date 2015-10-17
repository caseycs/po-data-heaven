<?php
use Dotenv\Dotenv;
use PODataHeaven\PoDataHeavenApplication;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new PoDataHeavenApplication();
$app->run();
