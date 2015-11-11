<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\CellFormatter\IdOfEntitiesFormatter;
use PODataHeaven\Container\ReportTreeNode;
use PODataHeaven\Exception\ClassNotFoundException;
use PODataHeaven\Exception\FormatterNotFoundException;
use PODataHeaven\Exception\LimitLessThenOneException;
use PODataHeaven\Exception\NoKeyFoundException;
use PODataHeaven\Exception\ParameterInvalidException;
use PODataHeaven\Exception\PODataHeavenException;
use PODataHeaven\Exception\ReportInvalidException;
use PODataHeaven\Exception\TransformerNotFoundException;
use PODataHeaven\GetParameterFromArrayKeyTrait;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;
use PODataHeaven\ObjectCreatorTrait;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ReportParserService
{
    use GetParameterFromArrayKeyTrait, ObjectCreatorTrait;

    /** @var ReportTreeNode */
    private $reportsTreeRoot;

    /** @var array */
    private $failedReports = [];

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return ReportTreeNode
     */
    public function getReportsTree()
    {
        $this->parseReports();
        return $this->reportsTreeRoot;
    }

    /**
     * @param string $baseName
     * @return Report
     */
    public function findOneByBaseName($baseName)
    {
        $this->parseReports();
        return $this->reportsTreeRoot->reports->findOneByBaseName($baseName);
    }

    /**
     * @throws ReportInvalidException
     */
    private function parseReports()
    {
        if (null !== $this->reportsTreeRoot) {
            return;
        }

        $this->reportsTreeRoot = new ReportTreeNode;

        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $this->fs->read($ymlFileMetadata['path']);

            try {
                $data = Yaml::parse($yml);
                $report = $this->buildReport($ymlFileMetadata['path'], $data);
                $this->reportsTreeRoot->reports->add($report);
            } catch (ParseException $e) {
                $this->failedReports[$ymlFileMetadata['path']] = $e->getMessage();
            } catch (PODataHeavenException $e) {
                $this->failedReports[$ymlFileMetadata['path']] = $e->getMessage();
            }
        }
    }

    /**
     * @param array $data
     * @return Report
     * @throws ReportInvalidException
     */
    private function buildReport($path, array $data)
    {
        $report = new Report();

        try {
            $report->filename = $path;
            $report->baseName = substr($path, 0, -4);
            $report->name = $this->getRequiredValue($data, 'name');
            $report->description = $this->getValue($data, 'description');
            $report->sql = $this->getRequiredValue($data, 'sql');

            if ($this->hasValue($data, 'limit')) {
                $limit = (int)$this->getValue($data, 'limit');
                if ($limit < 1) {
                    throw new LimitLessThenOneException;
                }
                $report->limit = $limit;
            }

            if ($this->hasValue($data, 'order')) {
                $report->order = $this->getValue($data, 'order');
            }

            $report->orientation = $this->getValue($data, 'orientation', Report::ORIENTATION_VERTICAL);
        } catch (LimitLessThenOneException $e) {
            throw new ReportInvalidException($path, $e);
        } catch (NoKeyFoundException $e) {
            throw new ReportInvalidException($path, $e);
        }

        foreach ($this->getValue($data, 'parameters', []) as $placeholder => $pData) {
            $pData = (array)$pData;

            $parameter = new Parameter();
            $parameter->placeholder = $placeholder;
            $parameter->name = $this->getValue($pData, 'name', $parameter->placeholder);
            $parameter->input = $this->getValue($pData, 'input', Parameter::INPUT_RAW);
            $parameter->idOfEntity = $this->getValue($pData, 'idOfEntity');
            $parameter->default = $this->getValue($pData, 'default');

            $report->parameters->add($parameter);
        }

        foreach ($this->getValue($data, 'columns', []) as $columnName => $cData) {
            $column = new Column();
            $column->name = $columnName;

            if (null !== $cData) {
                $column->align = $this->getValue($cData, 'align', Column::ALIGN_LEFT);
                $valid = [Column::ALIGN_LEFT, Column::ALIGN_RIGHT, Column::ALIGN_CENTER];
                if (!in_array($column->align, $valid, true)) {
                    throw new ParameterInvalidException('align', $column->align);
                }
            }

            $idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            if ([] !== $idOfEntities) {
                $column->formatter = new IdOfEntitiesFormatter(['idOfEntities' => $idOfEntities]);
                $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            } else {
                $formatterParameter = $this->getValue($cData, 'format', 'raw');

                try {
                    $column->formatter = $this->newObjectByClassName(
                        'CellFormatter',
                        $formatterParameter,
                        'Formatter'
                    );
                } catch (ClassNotFoundException $e) {
                    throw new FormatterNotFoundException($formatterParameter, $e);
                }

                $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            }

            $report->columns->add($column);
        }

        foreach ($this->getValue($data, 'transformers', []) as $transformerData) {
            $transformerName = key($transformerData);
            $tData = reset($transformerData);

            if (null === $tData) {
                $tData = [];
            }

            try {
                $transformer = $this->newObjectByClassName('ReportTransformer', $transformerName, 'Transformer', $tData);
            } catch (ClassNotFoundException $e) {
                throw new TransformerNotFoundException($transformerName, $e);
            }

            $report->transformers->add($transformer);
        }

        return $report;
    }

    /**
     * @return array
     */
    public function getFailedReports()
    {
        $this->parseReports();
        return $this->failedReports;
    }
}
