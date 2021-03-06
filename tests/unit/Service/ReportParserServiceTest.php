<?php
namespace PODataHeaven\Test\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit_Framework_TestCase;
use PODataHeaven\CellFormatter\IdOfEntitiesFormatter;
use PODataHeaven\CellFormatter\RawFormatter;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;
use PODataHeaven\Service\ReportParserService;

class ReportParserServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $filesystem;

    /** @var ReportParserService */
    private $service;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter());
        $this->service = new ReportParserService($this->filesystem, ['db']);
    }

    public function test_getReportsTree_one()
    {
        $this->filesystem->put('first.yml', 'name: Address details
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20
order: id ASC');

        $tree = $this->service->getReportsTree();
        $this->assertSame(1, $tree->reports->count());
    }

    public function test_getReportsTree_two()
    {
        $this->filesystem->put('first.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20
order: id ASC');
        $this->filesystem->put('second.yml', 'name: test2
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20
order: id ASC');

        $tree = $this->service->getReportsTree();
        $this->assertSame(2, $tree->reports->count());
    }

    public function test_getReportsTree_defaultMapping()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
sql: >
 SELECT * FROM `address` WHERE id = :id');

        $tree = $this->service->getReportsTree();
        $report = $tree->reports->first();

        $this->assertSame('test1', $report->name);
        $this->assertSame('first-report', $report->baseName);
        $this->assertSame(null, $report->description);
        $this->assertSame(Report::ORIENTATION_VERTICAL, $report->orientation);
        $this->assertSame(null, $report->order);
        $this->assertSame(null, $report->limit);
        $this->assertSame('SELECT * FROM `address` WHERE id = :id', trim($report->sql));

        $this->assertSame(false, $report->isHorizontal());
        $this->assertSame(true, $report->isVertical());
    }

    public function test_getReportsTree_limitMapping()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20');

        $tree = $this->service->getReportsTree();
        $report = $tree->reports->first();

        $this->assertSame(20, $report->limit);
    }

    public function test_getReportsTree_orderMapping()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

order: id ASC');

        $tree = $this->service->getReportsTree();
        $report = $tree->reports->first();

        $this->assertSame('id ASC', $report->order);
    }

    public function test_getReportsTree_ParameterMapping()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

parameters:
  p1:
  p2:
    name: p2 title
    input: raw
    idOfEntity: p2entity
    default: default

limit: 20
order: id ASC
');

        $tree = $this->service->getReportsTree();
        $report = $tree->reports->first();
        $this->assertSame(2, $report->parameters->count());

        $parameter = $report->parameters->first();
        $this->assertSame('p1', $parameter->placeholder);
        $this->assertSame('p1', $parameter->name);
        $this->assertSame(Parameter::INPUT_RAW, $parameter->input);

        $parameter = $report->parameters->offsetGet(1);
        $this->assertSame('p2', $parameter->placeholder);
        $this->assertSame('p2 title', $parameter->name);
        $this->assertSame(Parameter::INPUT_RAW, $parameter->input);
        $this->assertSame('default', $parameter->default);
        $this->assertSame('p2entity', $parameter->idOfEntity);
    }

    public function test_getReportsTree_ColumnMapping()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20
order: id ASC

columns:
  ic_session_id:
    idOfEntities: ic_session
  col2:
    format: url
    idOfEntities: [a, b]
');

        $tree = $this->service->getReportsTree();
        $report = $tree->reports->first();
        $this->assertSame(2, $report->columns->count());

        $column = $report->columns->first();
        $this->assertSame('ic_session_id', $column->name);
        $this->assertEquals(new IdOfEntitiesFormatter(['idOfEntities' => ['ic_session']]), $column->formatter);

        $column = $report->columns->offsetGet(1);
        $this->assertSame('col2', $column->name);
        $this->assertEquals(new IdOfEntitiesFormatter(['idOfEntities' => ['a', 'b']]), $column->formatter);
    }

    public function test_getReportsTree_ColumnAlign()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

limit: 20
order: id ASC

columns:
  id:
    formatter: url
  id2:
    align: right
');

        $tree = $this->service->getReportsTree();
        $column = $tree->reports->first()->columns->offsetGet(0);
        $this->assertSame(Column::ALIGN_LEFT, $column->align);

        $column = $tree->reports->first()->columns->offsetGet(1);
        $this->assertSame(Column::ALIGN_RIGHT, $column->align);
    }

    public function test_getReportsTree_ColumnOptions()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
sql: >
 SELECT * FROM `address` WHERE id = :id

columns:
  id:
    formatter: url
    options:
        a: b
');

        $tree = $this->service->getReportsTree();
        $column = $tree->reports->first()->columns->offsetGet(0);

        $this->assertFalse($tree->reports->first()->bold);
        $this->assertEquals(new RawFormatter(['a' => 'b']), $column->formatter);
    }

    public function test_getReportsTree_selected()
    {
        $this->filesystem->put('first-report.yml', 'name: test1
description: bla bla bla
bold: true
sql: >
 SELECT * FROM `address` WHERE id = :id
');

        $tree = $this->service->getReportsTree();
        $this->assertTrue($tree->reports->first()->bold);
    }
}
