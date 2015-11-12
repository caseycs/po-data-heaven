<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Connection;
use PDO;
use Pimple;
use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Exception\ParameterMissingException;
use PODataHeaven\Model\Report;
use PODataHeaven\ReportTransformer\TransformerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class ReportExecutorService
{
    /** @var Connection  */
    private $connection;

    /** @var MappingService */
    private $mappingService;

    /**
     * @param Pimple[] $connections
     * @param MappingService $mappingService
     */
    public function __construct(Pimple $connections, MappingService $mappingService)
    {
        $this->connections = $connections;
        $this->mappingService = $mappingService;
    }

    /**
     * @param Report $report
     * @param ParameterBag $paramsPassed
     * @return ReportExecutionResult
     * @throws ParameterMissingException
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
                throw new ParameterMissingException($p->placeholder);
            }
        }

        $sql = $this->buildSql($report);

        $connection = $this->connections[$report->connection];
        $rows = $connection->fetchAll($sql, $params, $paramsTypes);

        $rows = $this->transformRows($report, $rows);

        $result = new ReportExecutionResult();
        $result->rows = $rows;
        $result->parameters = $params;
        $result->sql = $this->postProcessSql($sql, $params);

        $this->mappingService->generateResultColumns($report, $result);

        return $result;
    }

    protected function transformRows(Report $report, array $rows)
    {
        /** @var TransformerInterface $transformer */
        foreach ($report->transformers as $transformer) {
            $rows = $transformer->transform($rows);
        }
        return $rows;
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
