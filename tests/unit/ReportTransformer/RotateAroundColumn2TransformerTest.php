<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\RotateAroundColumn2Transformer;

class RotateAroundColumn2Transformer2Test extends \PHPUnit_Framework_TestCase
{
    public function test_combine1column()
    {
        $params = ['pivot' => 'city', 'combine' => ['parameter'], 'value' => 'count'];
        $transformer = new RotateAroundColumn2Transformer($params);

        $input = [
            ['parameter' => 'men', 'city' => 'Amsterdam', 'count' => '10'],
            ['parameter' => 'woman','city' => 'Amsterdam',  'count' => '20'],
            ['parameter' => 'woman','city' => 'Rotterdam',  'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'parameter=men' => '10', 'parameter=woman' => '20'],
            ['city' => 'Rotterdam', 'parameter=men' => null, 'parameter=woman' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnNotFoundException
     */
    public function test_noPivotColumnPresented()
    {
        $params = ['pivot' => 'city1', 'combine' => ['parameter'], 'value' => 'count'];
        $transformer = new RotateAroundColumn2Transformer($params);

        $input = [
            ['parameter' => 'men', 'city' => 'Amsterdam', 'count' => '10'],
        ];

        $transformer->transform($input);
    }

    public function test_combine2columns()
    {
        $params = ['pivot' => 'city', 'combine' => ['parameter1', 'parameter2'], 'value' => 'count'];
        $transformer = new RotateAroundColumn2Transformer($params);

        $input = [
            ['city' => 'Rotterdam', 'parameter1' => 'men', 'parameter2' => 'men', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'woman', 'count' => '10'],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'ufo', 'count' => '10'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Rotterdam', 'parameter1=men,parameter2=men' => '10', 'parameter1=men,parameter2=woman' => null, 'parameter1=men,parameter2=ufo' => null],
            ['city' => 'Amsterdam', 'parameter1=men,parameter2=men' => null, 'parameter1=men,parameter2=woman' => '10', 'parameter1=men,parameter2=ufo' => '10'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_combine2columns2extraColumns()
    {
        $params = ['pivot' => 'city', 'combine' => ['parameter1', 'parameter2'], 'value' => 'count'];
        $transformer = new RotateAroundColumn2Transformer($params);

        $input = [
            ['city' => 'Rotterdam', 'parameter1' => 'men', 'parameter2' => 'men', 'count' => '10', 'e1' => 5, 'e2' => 10],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'woman', 'count' => '10', 'e1' => 6, 'e2' => 11],
            ['city' => 'Amsterdam', 'parameter1' => 'men', 'parameter2' => 'ufo', 'count' => '10', 'e1' => 6, 'e2' => 11],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Rotterdam', 'parameter1=men,parameter2=men' => '10', 'parameter1=men,parameter2=woman' => null, 'parameter1=men,parameter2=ufo' => null, 'e1' => 5, 'e2' => 10],
            ['city' => 'Amsterdam', 'parameter1=men,parameter2=men' => null, 'parameter1=men,parameter2=woman' => '10', 'parameter1=men,parameter2=ufo' => '10', 'e1' => 6, 'e2' => 11],
        ];

        $this->assertEquals($expected, $result);
    }
}
