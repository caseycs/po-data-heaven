<?php
namespace PODataHeaven\ServiceProvider;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PODataHeaven\Service\DbStructureGeneratorService;
use PODataHeaven\Service\DashboardParserService;
use PODataHeaven\Service\DenormalizerParserService;
use PODataHeaven\Service\MappingService;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use PODataHeaven\Service\ReportResultStorageService;
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

        //global template variables
        $te->addGlobal('editReportUrlPrefix', getenv('EDIT_REPORT_URL_PREFIX'));
        $te->addGlobal('editDashboardUrlPrefix', getenv('EDIT_DASHBOARD_URL_PREFIX'));

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

        $app['dashboard_filesystem.service'] = function () use ($app) {
            $adapter = new Local(getenv('DASHBOARDS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['denormalizer_filesystem.service'] = function () use ($app) {
            $adapter = new Local(getenv('DENORMALIZERS_DIR'));
            $filesystem = new Filesystem($adapter);
            return $filesystem;
        };

        $app['report_parser.service'] = function () use ($app) {
            return new ReportParserService($app['reports_filesystem.service']);
        };

        $app['dashboard_parser.service'] = function () use ($app) {
            return new DashboardParserService($app['dashboard_filesystem.service']);
        };

        $app['denormalizer_parser.service'] = function () use ($app) {
            return new DenormalizerParserService($app['denormalizer_filesystem.service']);
        };

        $app['mapping.service'] = function () use ($app) {
            return new MappingService($app['mappings_filesystem.service']);
        };

        $app['report_executor.service'] = function () use ($app) {
            return new ReportExecutorService($app['db'], $app['mapping.service']);
        };

        $app['db_structure_generator.service'] = function () use ($app) {
            return new DbStructureGeneratorService();
        };

        $app['report_result_storage.service'] = function () use ($app) {
            return new ReportResultStorageService($app['dbs']['reports'], $app['db_structure_generator.service']);
        };
    }

    public function boot(Application $app)
    {
    }
}
