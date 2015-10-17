<?php
namespace PODataHeaven\Test\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit_Framework_TestCase;
use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Report;
use PODataHeaven\Service\MappingService;

class MappingServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $filesystem;

    /** @var MappingService */
    private $mappingService;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter());
        $this->mappingService = new MappingService($this->filesystem);
    }

    public function test_getMappings_oneFile()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $mappings = $this->mappingService->getMappings();
        $this->assertTrue($mappings->offsetExists('user_id'));
        $this->assertSame(['user'], $mappings->offsetGet('user_id'));
    }

    public function test_getMappings_multipleFiles()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');
        $this->filesystem->put('second.yml', 'order_id: [order]');

        $mappings = $this->mappingService->getMappings();
        $this->assertTrue($mappings->offsetExists('user_id'));
        $this->assertSame(['user'], $mappings->offsetGet('user_id'));

        $this->assertTrue($mappings->offsetExists('order_id'));
        $this->assertSame(['order'], $mappings->offsetGet('order_id'));
    }

    public function test_getMappings_onePerLine()
    {
        $this->filesystem->put('first.yml', 'user_id: user');

        $mappings = $this->mappingService->getMappings();
        $this->assertTrue($mappings->offsetExists('user_id'));
        $this->assertSame(['user'], $mappings->offsetGet('user_id'));
    }

    public function test_getMappings_arrayPerLine()
    {
        $this->filesystem->put('first.yml', 'user_id: [user, consumer]');

        $mappings = $this->mappingService->getMappings();
        $this->assertTrue($mappings->offsetExists('user_id'));
        $this->assertSame(['user', 'consumer'], $mappings->offsetGet('user_id'));
    }

    public function test_generateResultColumns_createDefaultColumn()
    {
        $report = new Report();
        $reportResult = new ReportExecutionResult();
        $reportResult->rows = [['user_id' => 55]];
        $this->mappingService->generateResultColumns($report, $reportResult);

        $this->assertSame(1, $reportResult->columns->count());

        $column = new Column;
        $column->name = 'user_id';
        $this->assertEquals($column, $reportResult->columns->first());
    }

    public function test_generateResultColumns_addColumnFromMapping()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $report = new Report();
        $reportResult = new ReportExecutionResult();
        $reportResult->rows = [['user_id' => 55]];
        $this->mappingService->generateResultColumns($report, $reportResult);

        $this->assertSame(1, $reportResult->columns->count());

        $column = new Column;
        $column->name = 'user_id';
        $column->idOfEntities = ['user'];
        $this->assertEquals($column, $reportResult->columns->first());
    }

    public function test_generateResultColumns_updateMappingOfDefinedColumn()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $column = new Column();
        $column->name = 'user_id';
        $column->idOfEntities = ['consumer'];

        $report = new Report();
        $report->columns->add($column);


        $reportResult = new ReportExecutionResult();
        $reportResult->rows = [['user_id' => 55]];
        $this->mappingService->generateResultColumns($report, $reportResult);

        $this->assertSame(1, $reportResult->columns->count());

        $column = new Column;
        $column->name = 'user_id';
        $column->idOfEntities = ['consumer', 'user'];
        $this->assertEquals($column, $reportResult->columns->first());
    }
}
