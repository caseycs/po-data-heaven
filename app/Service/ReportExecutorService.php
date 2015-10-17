<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Connection;
use PODataHeaven\Collection\ReportExecutionResult;
use PODataHeaven\Exception\ReportParameterRequiredException;
use PODataHeaven\Model\Report;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReportExecutor
{
    /** @var Connection  */
    private $connection;

    /** @var MappingService */
    private $mappingService;

    public function __construct(Connection $connection, MappingService $mappingService)
    {
        $this->connection = $connection;
        $this->mappingService = $mappingService;
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

        $sql = $this->buildSql($report);

        $rows = $this->connection->fetchAll($sql, $params);

        $result = new ReportExecutionResult();
        $result->rows = $rows;
        $result->parameters = $params;
        $result->sql = $this->postProcessSql($sql, $params);

        //build report mapping
        if (count($result->rows)) {
            $this->mappingService->applyToReport(array_keys(reset($result->rows)), $report);
        }

        return $result;
    }

    /**
     * @param $sql
     * @param $params
     * @return string;
     */
    protected function postProcessSql($sql, $params)
    {
        $sqlGenerated = $sql;
        foreach ($params as $param => $value) {
            $sqlGenerated = str_replace(':' . $param, $this->connection->quote($value), $sqlGenerated);
        }
        return $sqlGenerated;
    }

    /**
     * @param Report $report
     * @return string
     */
    protected function buildSql(Report $report)
    {
        $sql = $report->sql;

        $sql .= ' ORDER BY ' . $report->order;
        $sql .= ' LIMIT ' . $report->limit;
        return $sql;
    }
}
