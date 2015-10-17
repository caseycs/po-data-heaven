<?php
namespace PODataHeaven\ServiceProvider;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PODataHeaven\Service\MappingService;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Silex\Application;
use Silex\ServiceProviderInterface;
use SqlFormatter;
use Twig_SimpleFilter;

class ApplicationProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /** @var \Twig_Environment $te */
        $te = $app['twig'];
        $filter = function ($a) {return SqlFormatter::format($a, true);};
        $te->addFilter(new Twig_SimpleFilter('sqlFormatter', $filter));

        $app['reports_filesystem.service'] = function () use ($app) {
            $adapter = new Local(getenv('REPORTS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['mappings_filesystem.service'] = function () use ($app) {
            $adapter = new Local(getenv('MAPPINGS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['report_parser.service'] = function () use ($app) {
            return new ReportParserService($app['reports_filesystem.service']);
        };

        $app['mapping.service'] = function () use ($app) {
            return new MappingService($app['mappings_filesystem.service']);
        };

        $app['report_executor.service'] = function () use ($app) {
            return new ReportExecutorService($app['db'], $app['mapping.service']);
        };
    }

    public function boot(Application $app)
    {
    }
}
