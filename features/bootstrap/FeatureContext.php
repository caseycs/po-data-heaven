<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\Client;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    public function __construct()
    {
        $app = new \PODataHeaven\PoDataHavenApplication(__DIR__ . '/../../test');
        $session = new Session(new BrowserKitDriver(new Client($app)));

        $mink = new Mink(['silex' => $session]);
        $mink->setDefaultSessionName('silex');

        $this->setMink($mink);
    }
}
