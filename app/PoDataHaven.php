<?php
namespace PODataHeaven;

use Dotenv\Dotenv;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use SqlFormatter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig_Filter_Function;

class PoDataHaven extends Application
{
    public function __construct($dotEnvDir)
    {
        parent::__construct();

        $dotenv = new Dotenv($dotEnvDir);
        $dotenv->load();

        $dotenv->required(['MYSQL_HOST', 'MYSQL_PORT', 'MYSQL_DBNAME', 'MYSQL_USER', 'MYSQL_PASSWORD']);

        $this['debug'] = true;

        $this->register(new TwigServiceProvider(),['twig.path' => __DIR__ . '/../twig']);

        /** @var \Twig_Environment $te */
        $te = $this['twig'];
        $te->addFilter(
            'sqlFormatter',
            new Twig_Filter_Function(
                function ($a) {
                    return SqlFormatter::format($a, true);
                }
            )
        );

        $dbParams = [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'host' => getenv('MYSQL_HOST'),
                'port' => getenv('MYSQL_PORT'),
                'dbname' => getenv('MYSQL_DBNAME'),
                'user' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
                'charset' => 'utf8mb4',
            ],
        ];

        $this->register(new DoctrineServiceProvider(), $dbParams);

        $this->register(new ServiceProvider());

        $app = $this;

        $this->get(
            '/',
            function () use ($app) {
                $data = ['reports' => $app['service.report_parser']->getReportsTree()->reports];
                return $app['twig']->render('index.twig', $data);
            }
        );

        $this->get(
            '/by-entity/{entities}/{entityId}',
            function ($entities, $entityId) use ($app) {

                $entities = array_filter(explode(',', $entities));

                $reportsOnlyWithOnlyOneParameter = $app['reports']->findWithOnlyOneEntity($entities);
                $reportsWithOtherParameters = $app['reports']->findWithEntityAndSomethingElse($entities);

                if (count($reportsOnlyWithOnlyOneParameter) === 1 && count($reportsWithOtherParameters) === 0) {
                    $first = reset($reportsOnlyWithOnlyOneParameter);
                    $url = "/report/{$first->report->baseName}/result?{$first->parameter->placeholder}={$entityId}";
                    return new RedirectResponse($url);
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

        $this->get(
            '/report/{baseName}',
            function ($baseName, Request $request) use ($app) {
                /** @var \PODataHeaven\Model\Report $report */
                $report = $app['reports']->findOneByBaseName($baseName);

                if ([] === $report->parameters) {
                    return new RedirectResponse("/report/{$baseName}/result");
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

        $this->get(
            '/report/{baseName}/result',
            function ($baseName, Request $request) use ($app) {
                $report = $app['reports']->findOneByBaseName($baseName);

                $parameters = $request->query;

                /** @var \PODataHeaven\Collection\ReportExecutionResult $reportExecutionResult */
                $reportExecutionResult = $app['service.report']->execute($report, $parameters);

                $templateData = [
                    'report' => $report,
                    'parameters' => $parameters->all(),
                    'rows' => $reportExecutionResult->rows,
                    'sql' => $reportExecutionResult->sql,
                    'csvUrl' => "/report/{$baseName}/csv?" . http_build_query($reportExecutionResult->parameters),
                ];

                return $app['twig']->render('reportResult.twig', $templateData);
            }
        );

        $this->get(
            '/report/{baseName}/csv',
            function ($baseName, Request $request) use ($app) {
                $report = $app['reports']->findOneByBaseName($baseName);

                $parameters = $request->query;

                /** @var \PODataHeaven\Collection\ReportExecutionResult $reportExecutionResult */
                $reportExecutionResult = $app['service.report']->execute($report, $parameters);

                //hack for Goodby\CSV
                error_reporting(~E_STRICT);

                $config = new ExporterConfig();
                $exporter = new Exporter($config);

                $date = date('m-d_h-i');
                \utilphp\util::force_download("{$baseName}-{$date}.csv");

                $rows2export = $reportExecutionResult->rows;
                array_unshift($rows2export, array_keys($rows2export[0]));

                $exporter->export('php://output', $rows2export);

                return '';
            }
        );

        $this->get(
            '/reports/tag/{tag}',
            function ($tag) use ($app) {
                return 'tag ' . $app->escape($tag);
            }
        );
    }
}
