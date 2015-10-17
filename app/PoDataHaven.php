<?php
namespace PODataHeaven;

use Dotenv\Dotenv;
use PODataHeaven\ServiceProvider\ApplicationProvider;
use PODataHeaven\ServiceProvider\ControllerProvider;
use PODataHeaven\ServiceProvider\EnvironmentProvider;
use PODataHeaven\ServiceProvider\RoutesProvider;
use Silex\Application;

class PoDataHaven extends Application
{
    public function __construct($dotEnvDir)
    {
        parent::__construct();

        $this['debug'] = true;

        $dotenv = new Dotenv($dotEnvDir);
        $dotenv->load();
        $dotenv->required(['DB_DRIVER']);

        $this->register(new EnvironmentProvider());
        $this->register(new ApplicationProvider());
        $this->register(new ControllerProvider());
        $this->register(new RoutesProvider()
        );
    }
}
