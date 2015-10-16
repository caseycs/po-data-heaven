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
    '/by-entity/{entity}/{entityId}',
    function ($entity, $entityId) use ($app) {
        $reportsOnlyWithOnlyOneParameter = $app['reports']->findWithOnlyEntity($entity);
        $reportsWithOtherParameters = $app['reports']->findWithEntityAndSomethingElse($entity);

        return $app['twig']->render(
            'byEntityId.twig',
            [
                'entity' => $entity,
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
        return $app['twig']->render(
            'reportConfig.twig',
            [
                'report' => $app['reports']->findOneByBaseName($baseName),
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

        $rows = $app['service.report']->execute($report, $parameters);

        $templateData = [
            'report' => $report,
            'parameters' => $parameters->all(),
            'rows' => $rows,
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
