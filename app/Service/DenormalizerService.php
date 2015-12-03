<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Exception;
use Psr\Log\LoggerInterface;

class DenormalizerService
{
    /**
     * @var Connection
     */
    private $sourceConnection;

    /**
     * @var Connection
     */
    private $targetConnection;

    /**
     * DenormalizerConsoleCommand constructor.
     * @param DenormalizerParserService $denormalizer
     * @param Connection $sourceConnection
     * @param Connection $targetConnection
     */
    public function __construct(
        Connection $sourceConnection,
        Connection $targetConnection
    ) {
        $this->sourceConnection = $sourceConnection;
        $this->targetConnection = $targetConnection;
    }

    /**
     * @param array $denormalizer
     * @param LoggerInterface $logger
     * @throws DBALException
     */
    public function denormalize(array $denormalizer, LoggerInterface $logger)
    {
        //drop tmp table
        $tableName = $denormalizer['resultTable'];
        $tableNameTmp = $tableName . '_tmp';

        $logger->info("dropping table $tableNameTmp");
        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->targetConnection->quoteIdentifier($tableNameTmp));
        $this->targetConnection->exec($sql);

        $min = $this->sourceConnection->fetchColumn($denormalizer['sqlMinId']);
        $max = $this->sourceConnection->fetchColumn($denormalizer['sqlMaxId']);

        $tableCreated = false;

        for ($i = $min; $i <= $max; $i += $denormalizer['batch']) {
            $maxTmp = $i + $denormalizer['batch'] - 1;
            $logger->info("fetching from $i to $maxTmp");

            $params = ['min' => $i, 'max' => $maxTmp];
            $chunk = $this->sourceConnection->fetchAll($denormalizer['sqlData'], $params);
            if (!count($chunk)) {
                continue;
            }

            if (!$tableCreated) {
                $this->createTable($tableNameTmp, $denormalizer, array_keys(reset($chunk)));
                $tableCreated = true;
            }

            //insert
            $logger->info("inserting from $i to $maxTmp");
            $sql = 'INSERT INTO ' . $this->targetConnection->quoteIdentifier($tableNameTmp) . 'VALUES ';

            foreach ($chunk as $row) {
                $sql .= '(';
                foreach ($row as $value) {
                    $sql .= null === $value ? 'NULL,' : $this->targetConnection->quote($value) . ',';
                }
                $sql = substr($sql, 0, -1) . '),';
            }
            $sql = substr($sql, 0, -1);
            $this->targetConnection->exec($sql);
        }

        //adding indexes
        foreach ($denormalizer['indexes'] as $indexColumns) {
            $logger->info("adding index: " . join(', ', $indexColumns));
            $sql = sprintf('alter table %s add index (%s)', $tableNameTmp, join(',', (array)$indexColumns));
            $this->targetConnection->exec($sql);
        }

        //drop normal table
        $logger->info("dropping table $tableName");
        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->targetConnection->quoteIdentifier($tableName));
        $this->targetConnection->exec($sql);

        //rename tmp table to normal one
        $logger->info("renaming table  $tableNameTmp to $tableName");
        $sql = sprintf(
            'ALTER TABLE %s RENAME %s',
            $this->targetConnection->quoteIdentifier($tableNameTmp),
            $this->targetConnection->quoteIdentifier($tableName)
        );
        $this->targetConnection->exec($sql);

        $logger->info("done");
    }

    /**
     * @param $tableNameTmp
     * @param array $config
     * @param array $firstRowKeys
     * @throws DBALException
     * @throws Exception
     */
    protected function createTable($tableNameTmp, $config, array $firstRowKeys)
    {
        $configuredColumns = array_keys($config['columns']);

        $tmp = array_diff($firstRowKeys, $configuredColumns);
        if ($tmp) {
            throw new Exception('columns not configured in yml: ' . join(', ', $tmp));
        }

        $tmp = array_diff($configuredColumns, $firstRowKeys);
        if ($tmp) {
            throw new Exception('columns not exists int the query: ' . join(', ', $tmp));
        }

        $schema = new Schema();
        $tableSchema = $schema->createTable($tableNameTmp);
        foreach ($firstRowKeys as $name) {
            $columnDetails = $config['columns'][$name];

            if (is_string($columnDetails)) {
                $type = $columnDetails;
                $options = [];
            } elseif (is_array($columnDetails)) {
                $type = $columnDetails['type'];
                $options = isset($columnDetails['options']) ? $columnDetails['options'] : [];
            } else {
                throw new Exception;
            }
            $tableSchema->addColumn($name, $type, $options);
        }
        $createQueries = $schema->toSql($this->targetConnection->getDatabasePlatform());

        foreach ($createQueries as $sql) {
            $this->targetConnection->exec($sql);
        }
    }
}
