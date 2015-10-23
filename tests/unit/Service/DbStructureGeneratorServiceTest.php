<?php
namespace PODataHeaven\Test\Service;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Mockery;
use PHPUnit_Framework_TestCase;
use PODataHeaven\Service\DbStructureGeneratorService;

class DbStructureGeneratorServiceTest extends PHPUnit_Framework_TestCase
{
    /** @var DbStructureGeneratorService */
    private $service;

    public function setUp()
    {
        $this->service = new DbStructureGeneratorService();
    }

    public function storeProvider()
    {
        return [
            'simple int' => [
                [
                    ['a' => '1'],
                    ['a' => '2'],
                ],
                ['CREATE TABLE tmp (a INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
            'int and string' => [
                [
                    ['a' => '1'],
                    ['a' => 'b'],
                ],
                ['CREATE TABLE tmp (a VARCHAR(1) NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
            'int and float' => [
                [
                    ['a' => '5'],
                    ['a' => '5.6'],
                ],
                ['CREATE TABLE tmp (a NUMERIC(2, 1) NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
            'int, float and null' => [
                [
                    ['a' => '5'],
                    ['a' => '5.6'],
                    ['a' => null],
                ],
                ['CREATE TABLE tmp (a NUMERIC(2, 1) DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
            'int, float, null, string' => [
                [
                    ['a' => '5'],
                    ['a' => '5.6'],
                    ['a' => null],
                    ['a' => 'fffff'],
                ],
                ['CREATE TABLE tmp (a VARCHAR(5) DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
            'multiple columns' => [
                [
                    ['a' => '5', 'b' => 'ffffff'],
                    ['a' => '5.6', 'b' => null],
                    ['a' => null, 'b' => null],
                    ['a' => 'fffff', 'b' => null],
                ],
                ['CREATE TABLE tmp (a VARCHAR(5) DEFAULT NULL, b VARCHAR(6) DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'],
            ],
        ];
    }

    /**
     * @dataProvider storeProvider
     */
    public function test_store(array $rows, array $sqlExpected)
    {
        $schema = new Schema();
        $tableSchema = $schema->createTable('tmp');

        $this->service->guessColumnTypes($rows, $tableSchema);

        $sql = $schema->toSql(new MySqlPlatform());
        $this->assertSame($sqlExpected, $sql);
    }
}
