<?php
namespace PODataHeaven;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['service.reports_filesystem'] = function ($name) use ($app) {
            $adapter = new Local(__DIR__.'/../report');
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['service.report'] = function ($name) use ($app) {
            return new ReportService($app['service.reports_filesystem'], $app['db']);
        };

        $app['reports'] = function ($name) use ($app) {
            return $app['service.report']->all();
        };
    }

    public function boot(Application $app)
    {
    }
}
