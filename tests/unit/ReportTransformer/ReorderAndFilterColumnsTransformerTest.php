<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\ReorderAndFilterColumnsTransformer;

class ReorderAndFilterColumnsTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function getExamples()
    {
        return [
            'just reorder' => [
                ['order' => ['a', 'b']],
                [['b' => 2, 'a' => 1]],
                [['a' => 1, 'b' => 2]],
            ],
            'reorder + filter' => [
                ['order' => ['a', 'c']],
                [['c' => 3, 'b' => 2, 'a' => 1]],
                [['a' => 1, 'c' => 3]],
            ],
            'just filter' => [
                ['order' => ['a']],
                [['b' => 2, 'a' => 1]],
                [['a' => 1]],
            ],
        ];
    }

    /**
     * @dataProvider getExamples
     */
    public function test_transform(array $params, array $rows, array $expectedResult)
    {
        $transformer = new ReorderAndFilterColumnsTransformer($params);
        $result = $transformer->transform($rows);
        $this->assertSame($expectedResult, $result);
    }
}
