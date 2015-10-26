<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\SumColumnsTransformer;

class SumColumnsTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_1column()
    {
        $params = ['source' => ['count'], 'result' => 'total'];
        $transformer = new SumColumnsTransformer($params);
        $input = [
            ['count' => '10'],
            ['count' => '20'],
            ['count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['count' => '10', 'total' => '10'],
            ['count' => '20', 'total' => '20'],
            ['count' => '30', 'total' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_2columns()
    {
        $params = ['source' => ['count', 'c'], 'result' => 'total'];
        $transformer = new SumColumnsTransformer($params);
        $input = [
            ['count' => '10', 'c' => 1],
            ['count' => '20', 'c' => 1],
            ['count' => '30', 'c' => 1],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['count' => '10', 'c' => 1, 'total' => '11'],
            ['count' => '20', 'c' => 1, 'total' => '21'],
            ['count' => '30', 'c' => 1, 'total' => '31'],
        ];

        $this->assertEquals($expected, $result);
    }
}
