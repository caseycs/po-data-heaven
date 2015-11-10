<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\Exception\ColumnNotFoundException;
use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ParameterMissingException;

class RotateAroundColumn2Transformer extends AbstractParameterContainer implements TransformerInterface
{
    public function transform(array $rows)
    {
        $pivotColumn = $this->getRequiredParameter('pivot');
        $countColumn = $this->getRequiredParameter('value');

        $combineColumns = $this->getRequiredParameter('combine');
        if (!is_array($combineColumns)) {
            throw new ParameterMissingException('combine');
        }

        list($rowsPreProcessed, $allColumnCombinations) = $this->preProcess($rows, $pivotColumn, $combineColumns, $countColumn);

        $result = [];
        foreach ($rowsPreProcessed as $key => $rowPreProcessed) {
            $row = $rowPreProcessed[1];
            $row[$pivotColumn] = $key;

            foreach ($allColumnCombinations as $columnsHash => $columns) {
                $columnName = join(',', $columns);
                $row[$columnName] = isset($rowPreProcessed[0][$columnsHash]) ? $rowPreProcessed[0][$columnsHash] : null;
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param array $rows
     * @param string $pivotColumn
     * @param array $combineColumns
     * @param string $countColumn
     * @return array
     */
    protected function preProcess(array $rows, $pivotColumn, array $combineColumns, $countColumn)
    {
        $result = $allColumnCombinations = [];
        foreach ($rows as $row) {
            if (!array_key_exists($pivotColumn, $row)) {
                throw new ColumnNotFoundException($pivotColumn);
            }

            $key = $row[$pivotColumn];
            unset($row[$pivotColumn]);

            $combineColumnsData = [];
            foreach ($combineColumns as $col) {
                if (!array_key_exists($col, $row)) {
                    throw new ColumnNotFoundException($col);
                }
                $combineColumnsData[] = $col . '=' . (null === $row[$col] ? 'NULL' : $row[$col]);
                unset($row[$col]);
            }

            $columnCombinationHash = md5(serialize($combineColumnsData));
            if (!isset($allColumnCombinations[$columnCombinationHash])) {
                $allColumnCombinations[$columnCombinationHash] = $combineColumnsData;
            }

            if (!isset($row[$countColumn])) {
                throw new ColumnNotFoundException($countColumn);
            }

            $result[$key][0][$columnCombinationHash] = $row[$countColumn];
            unset($row[$countColumn]);

            $result[$key][1] = $row;
        }
        return [$result, $allColumnCombinations];
    }
}
