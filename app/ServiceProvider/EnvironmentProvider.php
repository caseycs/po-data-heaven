<?php
namespace PODataHeaven\ServiceProvider;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\ServiceProviderInterface;

class EnvironmentProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new TwigServiceProvider,['twig.path' => __DIR__ . '/../../twig']);

        $dbParams = [
            'db.options' => [
                'driver' => getenv('DB_DRIVER'),
                'path' => getenv('DB_PATH'),
                'host' => getenv('DB_HOST'),
                'port' => getenv('DB_PORT'),
                'dbname' => getenv('DB_DBNAME'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'charset' => 'utf8mb4',
            ],
        ];
        $app->register(new DoctrineServiceProvider(), $dbParams);

        $app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => getenv('LOG_DIR') . '/application.log',
        ));
    }

    public function boot(Application $app)
    {
    }
}
