<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnAlreadyExistsException;
use PODataHeaven\Exception\ColumnNotFoundException;

class RenameColumnTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $from = $this->getRequiredScalarParameter('column');
        $to = $this->getRequiredScalarParameter('renameTo');

        foreach ($rows as &$row) {
            if (!array_key_exists($from, $row)) {
                d($row);
                throw new ColumnNotFoundException($from);
            }

            if (array_key_exists($to, $row)) {
                throw new ColumnAlreadyExistsException($to);
            }

            $row[$to] = $row[$from];
            unset($row[$from]);
        }
        return $rows;
    }
}
