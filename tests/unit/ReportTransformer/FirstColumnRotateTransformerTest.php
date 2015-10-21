<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\FirstColumnRotateTransformer;

class FirstColumnRotateTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_basic3columns()
    {
        $this->markTestIncomplete();
        $transformer = new FirstColumnRotateTransformer([]);

        $input = [
            ['city' => 'Amsterdam', 'parameter' => 'men', 'count' => 10],
            ['city' => 'Amsterdam', 'parameter' => 'woman', 'count' => 20],
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => 30],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'men' => '10', 'woman' => 20],
            ['city' => 'Rotterdam', 'men' => 'null', 'woman' => 30],
        ];

        $this->assertEquals($expected, $result);
    }
}
