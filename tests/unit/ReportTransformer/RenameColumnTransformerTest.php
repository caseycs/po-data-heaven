<?php
namespace PODataHeaven\Test\ReportTransformer;

use PODataHeaven\ReportTransformer\RenameColumnTransformer;

class RenameColumnTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function test_1column()
    {
        $params = ['column' => 'count', 'renameTo' => 'countNewName'];
        $transformer = new RenameColumnTransformer($params);
        $input = [
            ['count' => '10'],
            ['count' => '20'],
            ['count' => '30'],
        ];

        $result = $transformer->transform($input);

        $expected = [
            ['countNewName' => '10'],
            ['countNewName' => '20'],
            ['countNewName' => '30'],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \PODataHeaven\Exception\ColumnAlreadyExistsException
     */
    public function test_columnAlreadyExists()
    {
        $params = ['column' => 'count', 'renameTo' => 'countNewName'];
        $transformer = new RenameColumnTransformer($params);
        $input = [
            ['count' => '10', 'countNewName' => ''],
        ];

        $transformer->transform($input);
    }
}
