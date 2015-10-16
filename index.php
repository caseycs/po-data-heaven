<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = new \PODataHeaven\PoDataHaven(__DIR__, __DIR__ . '/reports');
$app->run();
