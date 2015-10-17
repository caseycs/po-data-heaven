<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\Collection\Collection;
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

    public function getMappings()
    {
        if (null === $this->mappings) {
            $this->init();
        }
        return $this->mappings;
    }

    public function applyToReport(array $columns, Report $report)
    {
        $mappings = $this->getMappings();

        foreach ($columns as $column) {
            if (!$mappings->containsKey($column)) {
                continue;
            }

            try {
                $columnModel = $report->columns->findByName($column);

                foreach ($mappings->offsetGet($column) as $entity) {
                    if (!in_array($entity, $columnModel->idOfEntities, true)) {
                        $columnModel->idOfEntities[] = $entity;
                    }
                }
            } catch (NoResultException $e) {
                $columnModel = new Column();
                $columnModel->name = $column;
                $columnModel->format = Column::FORMAT_RAW;
                $columnModel->idOfEntities = $mappings->offsetGet($column);

                $report->columns->add($columnModel);
            }
        }
    }
}
