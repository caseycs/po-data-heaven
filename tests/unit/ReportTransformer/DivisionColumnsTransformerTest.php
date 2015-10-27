<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\DivisionColumnsTransformer;

class DivisionColumnsTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $params = ['dividend' => 'a', 'divisor' => 'b', 'result' => 'c'];
        $transformer = new DivisionColumnsTransformer($params);
        $input = [
            ['a' => '30', 'b' => '15'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['a' => '30', 'b' => '15', 'c' => 2],
        ];

        $this->assertEquals($expected, $result);
    }
}
