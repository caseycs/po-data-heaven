<?php
namespace PODataHeaven;

use Doctrine\DBAL\Connection;
use League\Flysystem\Filesystem;
use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Collection\ReportExecutionResult;
use PODataHeaven\Exception\LimitLessThenOneException;
use PODataHeaven\Exception\NoKeyFoundException;
use PODataHeaven\Exception\ReportParameterRequiredException;
use PODataHeaven\Exception\ReportYmlInvalidException;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Yaml\Yaml;

class ReportService
{
    /** @var Connection  */
    private $connection;

    public function __construct(Filesystem $fs, Connection $connection)
    {
        $this->fs = $fs;
        $this->connection = $connection;
    }

    /**
     * @return ReportCollection
     * @throws ReportYmlInvalidException
     */
    public function all()
    {
        $tmp = [];

        foreach ($this->fs->listContents() as $ymlFileMetadata) {
            if (substr($ymlFileMetadata['path'], -4) !== '.yml') {
                continue;
            }

            $yml = $this->fs->read($ymlFileMetadata['path']);
            $data = Yaml::parse($yml);
            $tmp[] = $this->buildReport($ymlFileMetadata['path'], $data);
        }

        $reports = ReportCollection::create($tmp);
        return $reports;
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
            $parameter->input = $this->getValue($pData, 'input');
            $parameter->idOfEntity = $this->getValue($pData, 'idOfEntity');
            $parameter->default = $this->getValue($pData, 'default');

            $report->parameters[] = $parameter;
        }

        foreach ($this->getValue($data, 'columns', []) as $name => $cData) {
            $column = new Column();
            $column->name = $name;
            $column->format = $this->getValue($cData, 'format');
            $column->chop = $this->getValue($cData, 'chop', null);
            $column->idOfEntities = (array)$this->getValue($cData, 'idOfEntities');

            $report->columns[$name] = $column;
        }

        return $report;
    }

    /**
     * @param Report $report
     * @param ParameterBag $paramsPassed
     * @return ReportExecutionResult
     * @throws ReportParameterRequiredException
     */
    public function execute(Report $report, ParameterBag $paramsPassed)
    {
        $params = [];
        foreach ($report->parameters as $p) {
            if (!$paramsPassed->has($p->placeholder)) {
                throw new ReportParameterRequiredException($p->placeholder);
            }
            $params[$p->placeholder] = trim($paramsPassed->get($p->placeholder));
        }

        $sql = $report->sql;

        $sql .= ' ORDER BY ' . $report->order;
        $sql .= ' LIMIT ' . $report->limit;

        $rows = $this->connection->fetchAll($sql, $params);

        $result = new ReportExecutionResult();
        $result->rows = $rows;
        $result->sql = $sql;
        $result->parameters = $params;

        foreach ($params as $param => $value) {
            $result->sql = str_replace(':' . $param, $this->connection->quote($value), $result->sql);
        }

        return $result;
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
