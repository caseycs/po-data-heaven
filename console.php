<?php
use PODataHeaven\ConsoleCommand\DenormalizeConsoleCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use PODataHeaven\PoDataHeavenApplication;

$app = new PoDataHeavenApplication();

$console = new Application('My Silex Application', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console->add(new DenormalizeConsoleCommand($app['denormalizer_parser.service'], $app['denormalizer.service']));

return $console;
