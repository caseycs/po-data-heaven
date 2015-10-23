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
        $app->register(new TwigServiceProvider, ['twig.path' => __DIR__ . '/../../twig']);

        $dbParams = [
            'dbs.options' => [
                'db' => [
                    'driver' => getenv('DB_DRIVER'),
                    'path' => getenv('DB_PATH'),
                    'host' => getenv('DB_HOST'),
                    'port' => getenv('DB_PORT'),
                    'dbname' => getenv('DB_DBNAME'),
                    'user' => getenv('DB_USER'),
                    'password' => getenv('DB_PASSWORD'),
                    'charset' => 'utf8mb4',
                ],
            ],
        ];

        if (getenv('DB_REPORTS_DRIVER')) {
            $dbParams['dbs.options']['reports'] = [
                'driver' => getenv('DB_REPORTS_DRIVER'),
                'path' => getenv('DB_REPORTS_PATH'),
                'host' => getenv('DB_REPORTS_HOST'),
                'port' => getenv('DB_REPORTS_PORT'),
                'dbname' => getenv('DB_REPORTS_DBNAME'),
                'user' => getenv('DB_REPORTS_USER'),
                'password' => getenv('DB_REPORTS_PASSWORD'),
                'charset' => 'utf8mb4',
            ];
        }

        $app->register(new DoctrineServiceProvider(), $dbParams);

        $app->register(
            new MonologServiceProvider(),
            array(
                'monolog.logfile' => getenv('LOG_DIR') . '/application.log',
            )
        );
    }

    public function boot(Application $app)
    {
    }
}
