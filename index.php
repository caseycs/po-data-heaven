<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$dotenv->required('DATABASE_DSN');

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new \PODataHeaven\ServiceProvider());

$app->get('/', function() use($app) {
    var_dump($app['service.report']->all());
    return 'reports list';
});

$app->get('/report/{name}', function($name) use($app) {
    return 'report config'.$app->escape($name);
});

$app->get('/report/{name}/result', function($name) use($app) {
    return 'report result'.$app->escape($name);
});

$app->get('/reports/tag/{tag}', function($tag) use($app) {
    return 'tag '.$app->escape($tag);
});

$app->run();
