<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;

class DivisionColumnsTransformer extends AbstractParameterContainer implements TransformerInterface
{
    use AddColumnTrait;

    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $this->prepareAddColumn(reset($rows));

        $dividendColumn = $this->getRequiredParameter('dividend');
        $divisorColumn = $this->getRequiredParameter('divisor');
        $resultColumn = $this->getRequiredParameter('result');

        $result = [];
        foreach ($rows as $row) {
            if (!array_key_exists($dividendColumn, $row)) {
                throw new ColumnNotFoundException($dividendColumn);
            }
            if (!array_key_exists($divisorColumn, $row)) {
                throw new ColumnNotFoundException($divisorColumn);
            }

            if ($row[$divisorColumn] != 0) {
                $resultValue = $row[$dividendColumn] / $row[$divisorColumn];
            } else {
                $resultValue = null;
            }

            $row = $this->addColumn($row, $resultColumn, $resultValue);

            $result[] = $row;
        }
        return $result;
    }
}
