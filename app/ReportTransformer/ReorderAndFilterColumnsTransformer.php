<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;

class ReorderAndFilterColumnsTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $columnsOrder = $this->getRequiredArrayParameter('order');

        $result = [];
        foreach ($rows as $row) {
            $rowNew = [];

            foreach ($columnsOrder as $columnName) {
                if (!array_key_exists($columnName, $row)) {
                    throw new ColumnNotFoundException($columnName);
                }
                $rowNew[$columnName] = $row[$columnName];
            }
            $result[] = $rowNew;
        }
        return $result;
    }
}
