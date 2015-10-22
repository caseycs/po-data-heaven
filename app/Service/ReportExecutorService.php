<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Connection;
use PDO;
use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Exception\ReportParameterRequiredException;
use PODataHeaven\Model\Report;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReportExecutorService
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
        $paramsTypes = [];
        foreach ($report->parameters as $p) {
            if ($paramsPassed->has($p->placeholder)) {
                $params[$p->placeholder] = trim($paramsPassed->get($p->placeholder));
                $paramsTypes[$p->placeholder] = PDO::PARAM_INT;
            } elseif ($paramsPassed->has($p->placeholder. '-m')) {
                $values = explode("\n", $paramsPassed->get($p->placeholder . '-m'));
                $values = array_map('trim', $values);
                $values = array_filter($values);
                $params[$p->placeholder] = $values;
                $paramsTypes[$p->placeholder] = Connection::PARAM_INT_ARRAY;
            } else {
                throw new ReportParameterRequiredException($p->placeholder);
            }
        }

        $sql = $this->buildSql($report);

        $rows = $this->connection->fetchAll($sql, $params, $paramsTypes);

        $result = new ReportExecutionResult();
        $result->rows = $rows;
        $result->parameters = $params;
        $result->sql = $this->postProcessSql($sql, $params);

        $this->mappingService->generateResultColumns($report, $result);

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
            if (is_array($value)) {
                $connection = $this->connection;
                $quotedArray = array_map(function ($v) use ($connection) {return $connection->quote($v);}, $value);
                $valueQuoted = join(',', $quotedArray);
            } else {
                $valueQuoted = $this->connection->quote($value);
            }
            $sqlGenerated = str_replace(':' . $param, $valueQuoted, $sqlGenerated);
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

        if (null !== $report->order) {
            $sql .= ' ORDER BY ' . $report->order;
        }

        if (null !== $report->limit) {
            $sql .= ' LIMIT ' . $report->limit;
        }

        return $sql;
    }
}
