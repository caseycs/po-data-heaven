<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\Exception\ColumnNotFoundException;
use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ParameterMissingException;

class RotateAroundColumnTransformer extends AbstractParameterContainer implements TransformerInterface
{
    public function transform(array $rows)
    {
        $pivotColumn = $this->getRequiredParameter('pivotColumn');
        $countColumn = $this->getRequiredParameter('valueColumn');

        $combineColumns = $this->getRequiredParameter('combineColumns');
        if (!is_array($combineColumns)) {
            throw new ParameterMissingException('combineColumns');
        }

        list($rowsPreProcessed, $allColumnCombinations) = $this->preProcess($rows, $pivotColumn, $combineColumns, $countColumn);

        $result = [];
        foreach ($rowsPreProcessed as $key => $rowPreProcessed) {
            $row = $rowPreProcessed[1];
            $row[$pivotColumn] = $key;

            foreach ($allColumnCombinations as $columnsHash => $columns) {
                $columnName = join(', ', $columns);
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
            if (!isset($row[$pivotColumn])) {
                throw new ColumnNotFoundException($pivotColumn);
            }

            $key = $row[$pivotColumn];
            unset($row[$pivotColumn]);

            $combineColumnsData = [];
            foreach ($combineColumns as $col) {
                if (!isset($row[$col])) {
                    throw new ColumnNotFoundException($col);
                }
                $combineColumnsData[] = $row[$col];
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
