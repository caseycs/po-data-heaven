<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;

class EnsureColumnsPresentedTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $columns = $this->getRequiredArrayParameter('columns');

        foreach ($rows as &$row) {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $row)) {
                    $row[$column] = null;
                }
            }
        }
        return $rows;
    }
}
