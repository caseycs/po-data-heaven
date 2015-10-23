<?php
namespace PODataHeaven\Test\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Service\ReportResultStorageService;

class ReportResultStorageServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var Connection|MockInterface */
    private $connection;

    /** @var ReportResultStorageService */
    private $service;

    public function setUp()
    {
//        $this->connection = Mockery::mock(
//            'Doctrine\DBAL\Connection',
//            ['getDatabasePlatform' => new MySqlPlatform, 'quote' => null, 'exec' => null, 'insert' => null]
//        );
//        $this->service = new ReportResultStorageService($this->connection);
    }

    public function test_store()
    {
        $this->markTestIncomplete();
    }
}
