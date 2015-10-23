<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\CellFormatter\IdOfEntitiesFormatter;
use PODataHeaven\Container\ReportTreeNode;
use PODataHeaven\Exception\FormatterNotFoundException;
use PODataHeaven\Exception\LimitLessThenOneException;
use PODataHeaven\Exception\NoKeyFoundException;
use PODataHeaven\Exception\PODataHeavenException;
use PODataHeaven\Exception\ReportYmlInvalidException;
use PODataHeaven\Exception\TransformerNotFoundException;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ReportParserService
{
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
        if (null === $this->reportsTreeRoot) {
            $this->parseReports();
        }
        return $this->reportsTreeRoot;
    }

    /**
     * @throws ReportYmlInvalidException
     */
    private function parseReports()
    {
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
     * @throws ReportYmlInvalidException
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
            throw new ReportYmlInvalidException($path, $e);
        } catch (NoKeyFoundException $e) {
            throw new ReportYmlInvalidException($path, $e);
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

        foreach ($this->getValue($data, 'columns', []) as $name => $cData) {
            $column = new Column();
            $column->name = $name;

            $idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            if ([] !== $idOfEntities) {
                $column->formatter = new IdOfEntitiesFormatter(['idOfEntities' => $idOfEntities]);
                $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            } else {
                $formatterOptionValue = $this->getValue($cData, 'format', 'raw');
                $formatterClassName = '\\PODataHeaven\\CellFormatter\\' . ucfirst($formatterOptionValue) . 'Formatter';
                if (!class_exists($formatterClassName)) {
                    throw new FormatterNotFoundException($formatterOptionValue);
                }

                $column->formatter = new $formatterClassName();
                $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');
            }

            $report->columns->add($column);
        }

        foreach ($this->getValue($data, 'transformers', []) as $name => $tData) {
            $transformerClassName = '\\PODataHeaven\\ReportTransformer\\' . ucfirst($name) . 'Transformer';
            if (!class_exists($transformerClassName)) {
                throw new TransformerNotFoundException($name);
            }

            if (null === $tData) {
                $tData = [];
            }

            $transformer = new $transformerClassName($tData);
            $report->transformers->add($transformer);
        }

        return $report;
    }

    /**
     * @param array $data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function getValue(array $data, $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    /**
     * @param array $data
     * @param string $key
     * @return bool
     */
    private function hasValue(array $data, $key)
    {
        return isset($data[$key]);
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     * @throws NoKeyFoundException
     */
    private function getRequiredValue(array $data, $key)
    {
        if (!isset($data[$key]) || '' === trim($data[$key])) {
            throw new NoKeyFoundException($key);
        }
        return $data[$key];
    }

    /**
     * @return array
     */
    public function getFailedReports()
    {
        if (null === $this->reportsTreeRoot) {
            $this->parseReports();
        }
        return $this->failedReports;
    }
}
