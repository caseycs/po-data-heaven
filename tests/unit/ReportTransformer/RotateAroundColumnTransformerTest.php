<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\RotateAroundColumnTransformer;

class RotateAroundColumnTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_combine1column()
    {
        $params = ['pivotColumn' => 'city', 'combineColumns' => ['parameter'], 'valueColumn' => 'count'];
        $transformer = new RotateAroundColumnTransformer($params);

        $input = [
            ['parameter' => 'men', 'city' => 'Amsterdam', 'count' => '10'],
            ['parameter' => 'woman','city' => 'Amsterdam',  'count' => '20'],
            ['parameter' => 'woman','city' => 'Rotterdam',  'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'men' => '10', 'woman' => '20'],
            ['city' => 'Rotterdam', 'men' => null, 'woman' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnNotFoundException
     */
    public function test_noPivotColumnPresented()
    {
        $params = ['pivotColumn' => 'city1', 'combineColumns' => ['parameter'], 'valueColumn' => 'count'];
        $transformer = new RotateAroundColumnTransformer($params);

        $input = [
            ['parameter' => 'men', 'city' => 'Amsterdam', 'count' => '10'],
        ];

        $transformer->transform($input);
    }

    public function test_combine2columns()
    {
        $params = ['pivotColumn' => 'city', 'combineColumns' => ['parameter1', 'parameter2'], 'valueColumn' => 'count'];
        $transformer = new RotateAroundColumnTransformer($params);

        $input = [
            ['city' => 'Rotterdam', 'parameter1' => 'men', 'parameter2' => 'men', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'woman', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'ufo', 'count' => '10'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Rotterdam', 'men, men' => '10', 'men, woman' => null, 'men, ufo' => null],
            ['city' => 'Amsterdam', 'men, men' => null, 'men, woman' => '10', 'men, ufo' => '10'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_combine2columns2extraColumns()
    {
        $params = ['pivotColumn' => 'city', 'combineColumns' => ['parameter1', 'parameter2'], 'valueColumn' => 'count'];
        $transformer = new RotateAroundColumnTransformer($params);

        $input = [
            ['city' => 'Rotterdam', 'parameter1' => 'men', 'parameter2' => 'men', 'count' => '10', 'e1' => 5, 'e2' => 10],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'woman', 'count' => '10', 'e1' => 6, 'e2' => 11],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'ufo', 'count' => '10', 'e1' => 6, 'e2' => 11],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Rotterdam', 'men, men' => '10', 'men, woman' => null, 'men, ufo' => null, 'e1' => 5, 'e2' => 10],
            ['city' => 'Amsterdam', 'men, men' => null, 'men, woman' => '10', 'men, ufo' => '10', 'e1' => 6, 'e2' => 11],
        ];

        $this->assertEquals($expected, $result);
    }
}
