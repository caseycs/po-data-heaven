<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\FirstColumnRotateTransformer;

class FirstColumnRotateTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_1parameterColumn()
    {
        $transformer = new FirstColumnRotateTransformer([]);

        $input = [
            ['city' => 'Amsterdam', 'parameter' => 'men', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter' => 'woman', 'count' => '20'],
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'men' => '10', 'woman' => '20'],
            ['city' => 'Rotterdam', 'men' => null, 'woman' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_2parametersColumns()
    {
        $transformer = new FirstColumnRotateTransformer([]);

        $input = [
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'men', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter1' => 'ufo', 'parameter2' => 'men', 'count' => '10'],
            ['city' => 'Rotterdam', 'parameter3' => 'woman', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'men, men' => '10', 'ufo, men' => 10, 'woman' => null],
            ['city' => 'Rotterdam', 'men, men' => null, 'ufo, men' => null, 'woman' => 30],
        ];

        $this->assertEquals($expected, $result);
    }
}
