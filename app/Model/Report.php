<?php
namespace PODataHeaven\Model;

use Arrayzy\MutableArray;
use PODataHeaven\Collection\Collection;
use PODataHeaven\Collection\ColumnCollection;

class Report
{
    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    /** @var string */
    public $baseName, $name, $description, $sql, $order, $orientation;

    /** @var int */
    public $limit;

    /** @var ColumnCollection|Column[] */
    public $columns = [];

    /** @var MutableArray|Parameter[] */
    public $parameters = [];

    public function __construct()
    {
        $this->columns = new ColumnCollection();
        $this->parameters = new Collection();
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
