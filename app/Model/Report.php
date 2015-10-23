<?php
namespace PODataHeaven\Model;

use PODataHeaven\Collection\ColumnCollection;
use PODataHeaven\Collection\ParameterCollection;
use PODataHeaven\Collection\TransformerCollection;

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

    /** @var TransformerCollection[] */
    public $transformers = [];

    public function __construct()
    {
        $this->columns = new ColumnCollection();
        $this->parameters = new ParameterCollection();
        $this->transformers = new TransformerCollection();
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
