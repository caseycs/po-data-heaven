<?php
namespace PODataHeaven;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PODataHeaven\Service\MappingService;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['service.reports_filesystem'] = function () use ($app) {
            $adapter = new Local(getenv('REPORTS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['service.mappings_filesystem'] = function () use ($app) {
            $adapter = new Local(getenv('MAPPINGS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['service.report_parser'] = function () use ($app) {
            return new ReportParserService($app['service.reports_filesystem']);
        };

        $app['service.mapping'] = function () use ($app) {
            return new MappingService($app['service.mappings_filesystem']);
        };

        $app['service.report_executor'] = function () use ($app) {
            return new ReportExecutorService($app['db'], $app['service.mapping']);
        };
    }

    public function boot(Application $app)
    {
    }
}
