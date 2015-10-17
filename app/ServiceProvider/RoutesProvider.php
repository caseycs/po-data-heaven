<?php
namespace PODataHeaven\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class RoutesProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app->get('/', "index.controller:action");
        $app->get('/report/{baseName}', "report_config.controller:action");
        $app->get('/report/{baseName}/result', "report_html.controller:action");
        $app->get('/report/{baseName}/csv', "report_csv.controller:action");
        $app->get('/by-entity/{entities}/{entityId}', "report_by_entity.controller:action");
    }

    public function boot(Application $app)
    {
    }
}
