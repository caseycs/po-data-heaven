<?php
use PODataHeaven\ConsoleCommand\DenormalizerConsoleCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use PODataHeaven\PoDataHeavenApplication;

$app = new PoDataHeavenApplication();

$console = new Application('My Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console->add(new DenormalizerConsoleCommand($app['denormalizer_parser.service'], $app['db'], $app['dbs']['reports']));

return $console;
