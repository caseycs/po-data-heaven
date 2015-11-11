<?php
namespace PODataHeaven\ConsoleCommand;

use Doctrine\DBAL\Schema\Schema;
use PODataHeaven\Service\DenormalizerParserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;

class DenormalizerConsoleCommand extends Command
{
    /**
     * @var DenormalizerParserService
     */
    private $denormalizer;

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
        DenormalizerParserService $denormalizer,
        Connection $sourceConnection,
        Connection $targetConnection
    ) {
        parent::__construct();

        $this->denormalizer = $denormalizer;
        $this->sourceConnection = $sourceConnection;
        $this->targetConnection = $targetConnection;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pdh:denormalizer')
            ->setDescription('Data denormalizer')
            ->addArgument(
                'denormalizer',
                InputArgument::REQUIRED,
                'denormalizer filename without .yml extension'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('denormalizer');

        $config = $this->denormalizer->get($name);

        //drop tmp table
        $tableName = $config['resultTable'];
        $tableNameTmp = $tableName . '_tmp';

        $output->writeln("dropping table $tableNameTmp");
        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->targetConnection->quoteIdentifier($tableNameTmp));
        $this->targetConnection->exec($sql);

        $min = $this->sourceConnection->fetchColumn($config['sqlMinId']);
        $max = $this->sourceConnection->fetchColumn($config['sqlMaxId']);

        $tableCreated = false;

        for ($i = $min; $i <= $max; $i += $config['batch']) {
            $maxTmp = $i + $config['batch'] - 1;
            $output->writeln("fetching from $i to $maxTmp");

            $params = ['min' => $i, 'max' => $maxTmp];
            $chunk = $this->sourceConnection->fetchAll($config['sqlData'], $params);
            if (!count($chunk)) {
                continue;
            }

            if (!$tableCreated) {
                $this->createTable($tableNameTmp, $config, array_keys(reset($chunk)));
                $tableCreated = true;

            }

            //insert
            $output->writeln("inserting from $i to $maxTmp");
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
        foreach ($config['indexes'] as $indexColumns) {
            $output->writeln("adding index: " . join(', ' , $indexColumns));
            $sql = sprintf('alter table %s add index (%s)',
                $tableNameTmp,
                join(',', (array)$indexColumns));
            $this->targetConnection->exec($sql);
        }

        //drop normal table
        $output->writeln("dropping table $tableName");
        $sql = sprintf('DROP TABLE IF EXISTS %s', $this->targetConnection->quoteIdentifier($tableName));
        $this->targetConnection->exec($sql);

        //rename tmp table to normal one
        $output->writeln("renaming table  $tableNameTmp to $tableName");
        $sql = sprintf(
            'ALTER TABLE %s RENAME %s',
            $this->targetConnection->quoteIdentifier($tableNameTmp),
            $this->targetConnection->quoteIdentifier($tableName)
        );
        $this->targetConnection->exec($sql);

        $output->writeln("done");
    }

    protected function createTable($tableNameTmp, $config, array $firstRowKeys)
    {
        $configuredColumns = array_keys($config['columns']);

        $tmp = array_diff($firstRowKeys, $configuredColumns);
        if ($tmp) {
            throw new \Exception('columns not configured in yml: ' . join(', ', $tmp));
        }

        $tmp = array_diff($configuredColumns, $firstRowKeys);
        if ($tmp) {
            throw new \Exception('columns not exists int the query: ' . join(', ', $tmp));
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
                throw new \Exception;
            }
            $tableSchema->addColumn($name, $type, $options);
        }
        $createQueries = $schema->toSql($this->targetConnection->getDatabasePlatform());

        foreach ($createQueries as $sql) {
            $this->targetConnection->exec($sql);
        }
    }
}
