<?php
namespace PODataHeaven\ServiceProvider;

use PODataHeaven\Controller\DashboardController;
use PODataHeaven\Controller\IndexController;
use PODataHeaven\Controller\ReportByEntityController;
use PODataHeaven\Controller\ReportConfigController;
use PODataHeaven\Controller\ReportCsvController;
use PODataHeaven\Controller\ReportHtmlController;
use PODataHeaven\Controller\ReportJsonController;
use PODataHeaven\Controller\TmpController;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllerProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['index.controller'] = $app->share(
            function () use ($app) {
                return new IndexController(
                    $app['twig'], $app['report_parser.service'], $app['dashboard_parser.service']
                );
            }
        );
        $app['report_config.controller'] = $app->share(
            function () use ($app) {
                return new ReportConfigController($app['twig'], $app['report_parser.service']);
            }
        );
        $app['report_html.controller'] = $app->share(
            function () use ($app) {
                return new ReportHtmlController(
                    $app['twig'],
                    $app['report_parser.service'],
                    $app['report_executor.service']
                );
            }
        );
        $app['report_csv.controller'] = $app->share(
            function () use ($app) {
                return new ReportCsvController(
                    $app['twig'],
                    $app['report_parser.service'],
                    $app['report_executor.service']
                );
            }
        );

        $app['report_json.controller'] = $app->share(
            function () use ($app) {
                return new ReportJsonController(
                    $app['report_parser.service'],
                    $app['report_executor.service']
                );
            }
        );

        $app['report_by_entity.controller'] = $app->share(
            function () use ($app) {
                return new ReportByEntityController($app['twig'], $app['report_parser.service']);
            }
        );
        $app['dashboard.controller'] = $app->share(
            function () use ($app) {
                return new DashboardController(
                    $app['twig'],
                    $app['report_parser.service'],
                    $app['dashboard_parser.service'],
                    $app['report_executor.service']
                );
            }
        );
        $app['tmp.controller'] = $app->share(
            function () use ($app) {
                return new TmpController($app['report_result_storage.service']);
            }
        );
    }

    public function boot(Application $app)
    {
    }
}
