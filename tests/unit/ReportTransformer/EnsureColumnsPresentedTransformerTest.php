<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\EnsureColumnsPresentedTransformer;

class EnsureColumnsTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_1column()
    {
        $params = ['columns' => ['count1']];
        $transformer = new EnsureColumnsPresentedTransformer($params);
        $input = [
            ['count' => '10'],
            ['count' => '20'],
            ['count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['count' => '10', 'count1' => null],
            ['count' => '20', 'count1' => null],
            ['count' => '30', 'count1' => null],
        ];

        $this->assertEquals($expected, $result);
    }
}
