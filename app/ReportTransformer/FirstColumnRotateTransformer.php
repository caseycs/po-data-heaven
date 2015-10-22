<?php
namespace PODataHeaven\ReportTransformer;

use RuntimeException;

class FirstColumnRotateTransformer extends AbstractTransformer
{
    public function transform(array $rows)
    {
        list($rowsPreProcessed, $allColumnCombinations) = $this->preProcess($rows);

        $result = [];
        foreach ($rowsPreProcessed as $key => $rowPreProcessed) {
            $row = [key($rows[0]) => $key];
            foreach ($allColumnCombinations as $columnsHash => $columns) {
                $columnName = join(', ', $columns);
                $row[$columnName] = isset($rowPreProcessed[$columnsHash]) ? $rowPreProcessed[$columnsHash] : null;
            }
            $result[] = $row;
        }

        return $result;
    }

    /**
     * @param array $rows
     * @param $allColumns
     * @param $result
     * @return mixed
     */
    protected function preProcess(array $rows)
    {
        $result = $allColumnCombinations = [];
        foreach ($rows as $row) {
            if (count($row) < 3) {
                throw new RuntimeException;
            }

            $key = array_shift($row);
            $cnt = array_pop($row);

            $columnCombinationColumns = $row;
            $columnCombinationHash = md5(serialize($columnCombinationColumns));
            if (!isset($allColumnCombinations[$columnCombinationHash])) {
                $allColumnCombinations[$columnCombinationHash] = $columnCombinationColumns;
            }

            if (!isset($result[$key][$columnCombinationHash])) {
                $result[$key][$columnCombinationHash] = 0;
            }
            $result[$key][$columnCombinationHash] += $cnt;
        }
        return [$result, $allColumnCombinations];
    }
}
