<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\RemoveColumnTransformer;

class RemoveColumnTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_successSingleColumn()
    {
        $transformer = new RemoveColumnTransformer(['column' => 'parameter']);

        $input = [
            ['city' => 'Amsterdam', 'parameter' => 'men', 'count' => '10'],
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'count' => '10'],
            ['city' => 'Rotterdam', 'count' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_successMultipleColumns()
    {
        $transformer = new RemoveColumnTransformer(['columns' => ['parameter', 'count']]);

        $input = [
            ['city' => 'Amsterdam', 'parameter' => 'men', 'count' => '10'],
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam'],
            ['city' => 'Rotterdam'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ParameterMissingException
     */
    public function test_noRequiredParameter()
    {
        $transformer = new RemoveColumnTransformer();
        $transformer->transform([]);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnNotFoundException
     */
    public function test_columnNotFound()
    {
        $transformer = new RemoveColumnTransformer(['column' => 'parameter1']);

        $input = [
            ['city' => 'Amsterdam', 'parameter' => 'men', 'count' => '10'],
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $transformer->transform($input);
    }
}
