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
        $app->match('/report/{baseName}/result', "report_html.controller:action")->method('GET|POST');
        $app->match('/report/{baseName}/csv', "report_csv.controller:action")->method('GET|POST');
        $app->get('/by-entity/{entities}/{entityId}', "report_by_entity.controller:action");

        $app->get('/tmp', "tmp.controller:action");
    }

    public function boot(Application $app)
    {
    }
}
