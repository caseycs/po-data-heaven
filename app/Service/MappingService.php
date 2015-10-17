<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\Collection\Collection;
use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Report;
use Symfony\Component\Yaml\Yaml;

class MappingService
{
    /** @var Collection */
    private $mappings;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function init()
    {
        $this->mappings = new Collection();

        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $this->fs->read($ymlFileMetadata['path']);
            $data = Yaml::parse($yml);
            foreach ($data as $column => $entites) {
                $this->mappings->offsetSet($column, (array)$entites);
            }
        }
    }

    /**
     * @return Collection
     */
    public function getMappings()
    {
        if (null === $this->mappings) {
            $this->init();
        }
        return $this->mappings;
    }

    /**
     * @param Report $report
     * @param ReportExecutionResult $result
     */
    public function generateResultColumns(Report $report, ReportExecutionResult $result)
    {
        if (!$result->rows) {
            return;
        }

        $mappings = $this->getMappings();

        $columnsRaw = array_keys(reset($result->rows));
        foreach ($columnsRaw as $columnName) {
            //put column to result
            try {
                $column = $report->columns->findOneByName($columnName);
            } catch (NoResultException $e) {
                $column = new Column;
                $column->name = $columnName;
                $column->format = Column::FORMAT_RAW;
            }
            $result->columns->add($column);

            //fill mapping
            if (!$mappings->containsKey($columnName)) {
                continue;
            }

            foreach ($mappings->get($columnName) as $entityName) {
                if (!in_array($entityName, $column->idOfEntities, true)) {
                    $column->idOfEntities[] = $entityName;
                }
            }
        }
    }
}
