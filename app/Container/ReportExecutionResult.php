<?php
namespace PODataHeaven\Container;

use PODataHeaven\Collection\ColumnCollection;

class ReportExecutionResult
{
    /** @var ColumnCollection */
    public $columns;

    /** @var array */
    public $rows;

    /** @var string */
    public $sql;

    /** @var array */
    public $parameters;

    public function __construct()
    {
        $this->columns = new ColumnCollection();
    }
}
