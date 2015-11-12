<?php
namespace PODataHeaven\ReportTransformer;

use Exception;
use PODataHeaven\Exception\ColumnNotFoundException;

trait AddColumnTrait
{
    private $prepared = false;

    /**
     * @var int|false
     */
    private $beforeIndex;

    /**
     * @var int|false
     */
    private $afterIndex;

    /**
     * {@inheritdoc}
     */
    public function prepareAddColumn(array $firstRow)
    {
        $beforeColumn = $this->getParameter('before');
        $afterColumn = $this->getParameter('after');

        if ($afterColumn) {
            $this->afterIndex = array_search($afterColumn, array_keys($firstRow), true);
            if (false === $this->afterIndex) {
                throw new ColumnNotFoundException($afterColumn);
            }
        } elseif ($beforeColumn) {
            $this->beforeIndex = array_search($beforeColumn, array_keys($firstRow), true);
            if (false === $this->beforeIndex) {
                throw new ColumnNotFoundException($beforeColumn);
            }
        }

        $this->prepared = true;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn(array $row, $newColumnName, $newColumnContent)
    {
        if (!$this->prepared) {
            throw new Exception('Call prepareAddColumn first');
        }

        if ($this->afterIndex) {
            $before = array_slice($row, 0, $this->afterIndex + 1);
            $after = array_slice($row, $this->afterIndex + 1);
            $row = $before + [$newColumnName => $newColumnContent] + $after;
        } elseif ($this->beforeIndex) {
            $before = array_slice($row, 0, $this->beforeIndex);
            $after = array_slice($row, $this->beforeIndex);
            $row = $before + [$newColumnName => $newColumnContent] + $after;
        } else {
            $row[$newColumnName] = $newColumnContent;
        }

        return $row;
    }
}
