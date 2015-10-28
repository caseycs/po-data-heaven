<?php
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use PODataHeaven\Service\ReportResultStorageService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Dotenv\Dotenv;
use PODataHeaven\PoDataHeavenApplication;
use Symfony\Component\HttpFoundation\ParameterBag;

require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$app = new PoDataHeavenApplication();

$console = new Application('My Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('store-report-result')
    ->setDefinition(array(
         new InputArgument('report', InputArgument::REQUIRED),
         new InputArgument('table', InputArgument::REQUIRED),
         new InputOption('chunk', 'c', InputOption::VALUE_OPTIONAL, 'rows per batch insert query', '100'),
    ))
    ->setDescription('My command description')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        /** @var ReportParserService $reportService */
        $reportService = $app['report_parser.service'];

        /** @var ReportExecutorService $reportExecutorService */
        $reportExecutorService = $app['report_executor.service'];

        /** @var ReportResultStorageService $storageService */
        $storageService = $app['report_result_storage.service'];

        $output->writeln('Start');

        $report = $reportService->findOneByBaseName($input->getArgument('report'));

        $output->writeln('Executing report');
        $result = $reportExecutorService->execute($report, new ParameterBag());

        $output->writeln('Storing result rows');
        $storageService->store($result, $input->getArgument('table'), $input->getOption('chunk'));

        $output->writeln('Done');
    })
;

$console->run();
