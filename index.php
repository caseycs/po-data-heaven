<?php
use Dotenv\Dotenv;
use PODataHeaven\PoDataHavenApplication;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new PoDataHavenApplication();
$app->run();
