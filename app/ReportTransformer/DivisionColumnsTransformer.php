<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;

class DivisionColumnsTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $dividendColumn = $this->getRequiredParameter('dividend');
        $divisorColumn = $this->getRequiredParameter('divisor');
        $resultColumn = $this->getRequiredParameter('result');

        $result = [];
        foreach ($rows as $row) {
            if (!isset($row[$dividendColumn])) {
                throw new ColumnNotFoundException($dividendColumn);
            }
            if (!isset($row[$divisorColumn])) {
                throw new ColumnNotFoundException($divisorColumn);
            }
            $row[$resultColumn] = $row[$dividendColumn] / $row[$divisorColumn];
            $result[] = $row;
        }
        return $result;
    }
}
