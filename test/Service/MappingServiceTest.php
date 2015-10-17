<?php
namespace PODataHeaven\Test\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit_Framework_TestCase;
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

    public function test_applyToReport_newColumn()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $report = new Report();
        $this->mappingService->applyToReport(['user_id', 'data'], $report);

        $this->assertCount(1, $report->columns);

        $column = new Column();
        $column->name = 'user_id';
        $column->format = Column::FORMAT_RAW;
        $column->idOfEntities = ['user'];
        $this->assertEquals($column, $report->columns->first());
    }

    public function test_applyToReport_existedColumn()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $column = new Column();
        $column->name = 'user_id';
        $column->format = Column::FORMAT_RAW;
        $column->idOfEntities = ['user'];

        $report = new Report();
        $report->columns->add($column);

        $this->mappingService->applyToReport(['user_id', 'data'], $report);

        $this->assertCount(1, $report->columns);
        $this->assertSame($column, $report->columns->first());
    }

    public function test_applyToReport_noMatch()
    {
        $this->filesystem->put('first.yml', 'user_id: [user]');

        $column = new Column();
        $column->name = 'order_id';
        $column->format = Column::FORMAT_RAW;
        $column->idOfEntities = ['order'];

        $report = new Report();
        $report->columns->add($column);

        $this->mappingService->applyToReport(['order_id', 'data'], $report);

        $this->assertCount(1, $report->columns);
        $this->assertSame($column, $report->columns->first());
    }
}
