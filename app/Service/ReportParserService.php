<?php
namespace PODataHeaven\Service;

use League\Flysystem\Filesystem;
use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Container\ReportTreeNode;
use PODataHeaven\Exception\LimitLessThenOneException;
use PODataHeaven\Exception\NoKeyFoundException;
use PODataHeaven\Exception\ReportYmlInvalidException;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;
use Symfony\Component\Yaml\Yaml;

class ReportParserService
{
    /** @var ReportTreeNode */
    private $reportsTreeRoot;

    public function __construct(Filesystem $reportsFs)
    {
        $this->reports = new ReportCollection;

        foreach ($reportsFs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $reportsFs->read($ymlFileMetadata['path']);
            $data = Yaml::parse($yml);
            $this->reports->push($this->buildReport($ymlFileMetadata['path'], $data));
        }
    }

    /**
     * @return ReportTreeNode
     */
    public function getReportsTree()
    {
        return $this->reportsTreeRoot;
    }

    /**
     * @param array $data
     * @return Report
     * @throws \Exception
     */
    private function buildReport($path, array $data)
    {
        $report = new Report();

        try {
            $report->baseName = substr($path, 0, -4);
            $report->name = $this->getRequiredValue($data, 'name');
            $report->description = $this->getRequiredValue($data, 'description');
            $report->sql = $this->getRequiredValue($data, 'sql');

            $limit = (int)$this->getRequiredValue($data, 'limit');
            if ($limit < 1) {
                throw new LimitLessThenOneException;
            }
            $report->limit = $limit;

            $report->order = $this->getRequiredValue($data, 'order');
            $report->orientation = $this->getRequiredValue($data, 'orientation');
        } catch (LimitLessThenOneException $e) {
            throw new ReportYmlInvalidException($path, $e);
        } catch (NoKeyFoundException $e) {
            throw new ReportYmlInvalidException($path, $e);
        }

        foreach ($this->getValue($data, 'parameters', []) as $placeholder => $pData) {
            $parameter = new Parameter();
            $parameter->placeholder = $placeholder;
            $parameter->name = $this->getRequiredValue($pData, 'name');
            $parameter->input = $this->getValue($pData, 'input', Parameter::INPUT_RAW);
            $parameter->idOfEntity = $this->getValue($pData, 'idOfEntity');
            $parameter->default = $this->getValue($pData, 'default');

            $report->parameters->push($parameter);
        }

        foreach ($this->getValue($data, 'columns', []) as $name => $cData) {
            $column = new Column();
            $column->name = $name;
            $column->format = $this->getValue($cData, 'format', Column::FORMAT_RAW);
            $column->chop = $this->getValue($cData, 'chop', null);
            $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');

            $report->columns->offsetSet($name, $column);
        }

        return $report;
    }

    private function getValue(array $data, $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    private function getRequiredValue(array $data, $key)
    {
        if (!isset($data[$key]) || '' === trim($data[$key])) {
            throw new NoKeyFoundException($key);
        }
        return $data[$key];
    }
}
