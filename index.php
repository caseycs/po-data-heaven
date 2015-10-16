<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$dotenv->required(['MYSQL_HOST', 'MYSQL_PORT', 'MYSQL_DBNAME', 'MYSQL_USER', 'MYSQL_PASSWORD']);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => __DIR__ . '/twig',
    ]
);

$app->register(
    new Silex\Provider\DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver' => 'pdo_mysql',
            'host' => getenv('MYSQL_HOST'),
            'port' => getenv('MYSQL_PORT'),
            'dbname' => getenv('MYSQL_DBNAME'),
            'user' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8mb4',
        ],
    ]
);

$app->register(new \PODataHeaven\ServiceProvider());

$app->get(
    '/',
    function () use ($app) {
//    ddd($app['reports']);
        return $app['twig']->render(
            'index.twig',
            [
                'reports' => $app['reports'],
            ]
        );
    }
);

$app->get(
    '/by-entity/{entities}/{entityId}',
    function ($entities, $entityId) use ($app) {

        $entities = array_filter(explode(',', $entities));

        $reportsOnlyWithOnlyOneParameter = $app['reports']->findWithOnlyOneEntity($entities);
        $reportsWithOtherParameters = $app['reports']->findWithEntityAndSomethingElse($entities);

        if (count($reportsOnlyWithOnlyOneParameter) === 1 && count($reportsWithOtherParameters) === 0) {
            $first = reset($reportsOnlyWithOnlyOneParameter);
            $url = "/report/{$first->report->baseName}/result?{$first->parameter->placeholder}={$entityId}";
            return new \Symfony\Component\HttpFoundation\RedirectResponse($url);
        }

        return $app['twig']->render(
            'byEntityId.twig',
            [
                'entities' => $entities,
                'entityId' => $entityId,
                'reports' => [
                    'only' => $reportsOnlyWithOnlyOneParameter,
                    'rest' => $reportsWithOtherParameters,
                ]
            ]
        );
    }
);

$app->get(
    '/report/{baseName}',
    function ($baseName, \Symfony\Component\HttpFoundation\Request $request) use ($app) {
        /** @var \PODataHeaven\Model\Report $report */
        $report = $app['reports']->findOneByBaseName($baseName);

        if ([] === $report->parameters) {
            return new \Symfony\Component\HttpFoundation\RedirectResponse("/report/{$baseName}/result");
        }

        return $app['twig']->render(
            'reportConfig.twig',
            [
                'report' => $report,
                'parameters' => $request->query,
            ]
        );
    }
);

$app->get(
    '/report/{baseName}/result',
    function ($baseName, \Symfony\Component\HttpFoundation\Request $request) use ($app) {
        $report = $app['reports']->findOneByBaseName($baseName);

        $parameters = $request->query;

        /** @var \PODataHeaven\Collection\ReportExecutionResult $reportExecutionResult */
        $reportExecutionResult = $app['service.report']->execute($report, $parameters);

        $templateData = [
            'report' => $report,
            'parameters' => $parameters->all(),
            'rows' => $reportExecutionResult->rows,
            'sql' => $reportExecutionResult->sql,
        ];

        return $app['twig']->render('reportResult.twig', $templateData);
    }
);

$app->get(
    '/reports/tag/{tag}',
    function ($tag) use ($app) {
        return 'tag ' . $app->escape($tag);
    }
);

$app->run();
