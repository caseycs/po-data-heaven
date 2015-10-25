<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\AddTwigColumnTransformer;

class AddTwigColumnTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_successColumnInTheMiddle()
    {
        $transformer = new AddTwigColumnTransformer(['after' => 'city', 'name' => 'new', 'template' => "aaa {{row['city']}}"]);

        $input = [
            ['city' => 'Amsterdam', 'count' => '10'],
            ['city' => 'Rotterdam', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'new' => 'aaa Amsterdam', 'count' => '10'],
            ['city' => 'Rotterdam', 'new' => 'aaa Rotterdam', 'count' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_successLastColumn()
    {
        $transformer = new AddTwigColumnTransformer(['after' => 'count', 'name' => 'new', 'template' => "aaa {{row['city']}}"]);

        $input = [
            ['city' => 'Amsterdam', 'count' => '10'],
            ['city' => 'Rotterdam', 'count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['city' => 'Amsterdam', 'count' => '10', 'new' => 'aaa Amsterdam'],
            ['city' => 'Rotterdam', 'count' => '30', 'new' => 'aaa Rotterdam'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function notEnoughParameters()
    {
        return [
            [[]],
            [['after' => 'a']],
            [['after' => 'a', 'name' => 'b']],
            [['after' => 'a', 'template' => 'b']],
            [['template' => 'b']],
            [['name' => 'b']],
        ];
    }

    /**
     * @dataProvider notEnoughParameters
     * @expectedException \PODataHeaven\Exception\ParameterMissingException
     */
    public function test_noRequiredParameter(array $parameters)
    {
        $transformer = new AddTwigColumnTransformer($parameters);
        $transformer->transform([]);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnNotFoundException
     */
    public function test_columnNotFound()
    {
        $transformer = new AddTwigColumnTransformer(['after' => 'a', 'name' => 'n', 'template' => 'b']);

        $input = [
            ['city' => 'Rotterdam', 'parameter' => 'woman', 'count' => '30'],
        ];

        $transformer->transform($input);
    }
}
