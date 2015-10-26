<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;

class SumColumnsTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $sourceColumns = (array)$this->getRequiredParameter('source');
        $resultColumn = $this->getRequiredParameter('result');

        $result = [];
        foreach ($rows as $row) {
            $sum = null;
            foreach ($sourceColumns as $key) {
                if (!isset($row[$key])) {
                    throw new ColumnNotFoundException($key);
                }
                if ($sum === null) {
                    $sum = $row[$key];
                } else {
                    $sum += $row[$key];
                }
            }
            $row[$resultColumn] = $sum;
            $result[] = $row;
        }
        return $result;
    }
}
