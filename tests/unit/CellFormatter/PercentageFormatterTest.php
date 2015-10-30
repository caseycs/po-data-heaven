<?php
namespace PODataHeaven\Test\CellFormatter;

use PODataHeaven\CellFormatter\PercentageFormatter;

class PercentageFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function test_successRowAsArray()
    {
        $formatter = new PercentageFormatter;
        $this->assertSame('50%', $formatter->format(.5));
    }
}
