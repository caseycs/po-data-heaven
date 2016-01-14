<?php
namespace PODataHeaven\Test\CellFormatter;

use PODataHeaven\CellFormatter\MinutesSecondsFormatter;

class MinutesSecondsFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function getExamples()
    {
        return [
            ['5', '0:05'],
            ['65', '1:05'],
            ['85', '1:25'],
            ['605', '10:05'],
        ];
    }

    /**
     * @dataProvider getExamples
     */
    public function testValue($input, $expected)
    {
        $formatter = new MinutesSecondsFormatter([]);
        $this->assertSame($expected, $formatter->format($input));
    }
}
