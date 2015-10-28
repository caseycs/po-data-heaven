<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use PODataHeaven\Container\ReportExecutionResult;

class ReportResultStorageService
{
    /** @var Connection */
    private $connection;

    /** @var DbStructureGeneratorService */
    private $dbStructureGeneratorService;

    public function __construct(Connection $connection, DbStructureGeneratorService $dbStructureGeneratorService)
    {
        $this->connection = $connection;
        $this->dbStructureGeneratorService = $dbStructureGeneratorService;
    }

    public function store(ReportExecutionResult $result, $tableName, $chunkSize = 1000)
    {
        $tableNameTmp = $tableName . '_tmp';

        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->connection->quoteIdentifier($tableNameTmp));
        $this->connection->exec($sql);

        $schema = new Schema();
        $tableSchema = $schema->createTable($tableNameTmp);
        $this->dbStructureGeneratorService->guessColumnTypes($result->rows, $tableSchema, $this->connection);
        $createQueries = $schema->toSql($this->connection->getDatabasePlatform());

//        ddd($createQueries);
        foreach ($createQueries as $sql) {
//            d($sql);
            $this->connection->exec($sql);
        }

        while ($chunk = array_splice($result->rows, 0, $chunkSize)) {
            $sql = 'INSERT INTO ' . $this->connection->quoteIdentifier($tableNameTmp) . 'VALUES ';

            foreach ($chunk as $row) {
                $sql .= '(';
                foreach ($row as $value) {
                    $sql .= null === $value ? 'NULL,' : $this->connection->quote($value) . ',';
                }
                $sql = substr($sql, 0, -1) . '),';
            }
            $sql = substr($sql, 0, -1);
            $this->connection->exec($sql);
        }

        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->connection->quoteIdentifier($tableName));
        $this->connection->exec($sql);

        $sql = sprintf(
            'ALTER TABLE %s RENAME %s',
            $this->connection->quoteIdentifier($tableNameTmp),
            $this->connection->quoteIdentifier($tableName)
        );
        $this->connection->exec($sql);
    }
}
