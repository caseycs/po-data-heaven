<?php
namespace PODataHeaven\Model;

use PODataHeaven\Collection\ColumnCollection;
use PODataHeaven\Collection\ParameterCollection;

class Report
{
    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    /** @var string */
    public $filename, $baseName, $name, $description, $sql, $order, $orientation;

    /** @var int */
    public $limit;

    /** @var ColumnCollection|Column[] */
    public $columns = [];

    /** @var ParameterCollection[] */
    public $parameters = [];

    public function __construct()
    {
        $this->columns = new ColumnCollection();
        $this->parameters = new ParameterCollection();
    }

    /**
     * @return bool
     */
    public function isVertical()
    {
        return $this->orientation === self::ORIENTATION_VERTICAL;
    }

    /**
     * @return bool
     */
    public function isHorizontal()
    {
        return $this->orientation === self::ORIENTATION_HORIZONTAL;
    }
}
