<?php
namespace PODataHeaven\Test;

use Silex\WebTestCase as SilexWebTestCase;

abstract class WebTestCase extends SilexWebTestCase
{
    public function createApplication()
    {
        return new \PODataHeaven\PoDataHaven(__DIR__);
    }
}
