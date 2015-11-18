<?php
namespace PODataHeaven\Test\CellFormatter;

use PODataHeaven\CellFormatter\MysqlDateFormatter;

class MysqlDateFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function getExamples()
    {
        return [
            ['2000-01-01 10:10:10', [], '<span title="2000-01-01 10:10:10">01.01.00 10:10</span>'],
            ['2000-01-01 10:10:10', ['format' => 'd.m.y'], '<span title="2000-01-01 10:10:10">01.01.00</span>'],
            ['2000-01-01 10:10:10', ['format' => 'd.m.Y'], '<span title="2000-01-01 10:10:10">01.01.2000</span>'],
        ];
    }

    /**
     * @dataProvider getExamples
     */
    public function testValue($input, array $parameters, $expected)
    {
        $formatter = new MysqlDateFormatter($parameters);
        $this->assertSame($expected, $formatter->format($input));
    }
}
