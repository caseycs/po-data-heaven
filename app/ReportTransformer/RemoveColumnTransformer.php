<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;
use PODataHeaven\Exception\ParameterMissingException;

class RemoveColumnTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        if ($this->hasParameter('column')) {
            $keysToRemove = [$this->getParameter('column')];
        } elseif ($this->hasParameter('columns')) {
            $keysToRemove = $this->getParameter('columns');
        } else {
            throw new ParameterMissingException('Either column on columns parameter should be specified');
        }

        $result = [];
        foreach ($rows as $row) {
            foreach ($keysToRemove as $keyToRemove) {
                if (!isset($row[$keyToRemove])) {
                    throw new ColumnNotFoundException($keyToRemove);
                }
                unset($row[$keyToRemove]);
            }
            $result[] = $row;
        }
        return $result;
    }
}
