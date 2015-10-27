<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\AddTwigExpressionColumnTransformer;

class AddTwigExpressionColumnTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_successAfter()
    {
        $transformer = new AddTwigExpressionColumnTransformer(['after' => 'city', 'name' => 'new', 'expression' => "city ~ ' A'"]);

        $input = [
            ['city' => 'Amsterdam'],
            ['city' => 'Rotterdam'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'new' => 'Amsterdam A'],
            ['city' => 'Rotterdam', 'new' => 'Rotterdam A'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_successDefaultLast()
    {
        $transformer = new AddTwigExpressionColumnTransformer(['name' => 'new', 'expression' => "city ~ ' b'"]);

        $input = [
            ['city' => 'Amsterdam'],
            ['city' => 'Rotterdam'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'new' => 'Amsterdam b'],
            ['city' => 'Rotterdam', 'new' => 'Rotterdam b'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function notEnoughParameters()
    {
        return [
            [[]],
            [['after' => 'a']],
            [['before' => 'a']],
            [['after' => 'a', 'name' => 'b']],
            [['before' => 'a', 'name' => 'b']],
            [['after' => 'a', 'expression' => 'b']],
            [['before' => 'a', 'expression' => 'b']],
            [['expression' => 'b']],
            [['name' => 'b']],
        ];
    }

    /**
     * @dataProvider notEnoughParameters
     * @expectedException \PODataHeaven\Exception\ParameterMissingException
     */
    public function test_noRequiredParameter(array $parameters)
    {
        $transformer = new AddTwigExpressionColumnTransformer($parameters);
        $transformer->transform([]);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnNotFoundException
     */
    public function test_columnNotFound()
    {
        $transformer = new AddTwigExpressionColumnTransformer(['after' => 'a', 'name' => 'n', 'expression' => 'b']);

        $input = [
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $transformer->transform($input);
    }

    /**
     * @expectedException \PODataHeaven\Exception\InvalidExpressionException
     */
    public function test_invalidExpression()
    {
        $transformer = new AddTwigExpressionColumnTransformer(['name' => 'new', 'expression' => "city {{ A '"]);

        $input = [
            ['city' => 'Rotterdam'],
        ];

        $transformer->transform([$input]);
    }
}
