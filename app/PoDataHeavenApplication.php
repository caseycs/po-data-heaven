<?php
namespace PODataHeaven;

use PODataHeaven\ServiceProvider\ApplicationProvider;
use PODataHeaven\ServiceProvider\ControllerProvider;
use PODataHeaven\ServiceProvider\EnvironmentProvider;
use PODataHeaven\ServiceProvider\RoutesProvider;
use Silex\Application;

class PoDataHeavenApplication extends Application
{
    public function __construct()
    {
        parent::__construct();

        $this['debug'] = getenv('APP_DEBUG');

        $this->register(new EnvironmentProvider());
        $this->register(new ApplicationProvider());
        $this->register(new ControllerProvider());
        $this->register(new RoutesProvider());
    }
}
