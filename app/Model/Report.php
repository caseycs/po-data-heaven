<?php
namespace PODataHeaven\Model;

class Report
{
    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    /** @var string */
    public $baseName, $name, $description, $sql, $order, $orientation;

    /** @var int */
    public $limit;

    /** @var Column[] */
    public $columns = [];

    /** @var Parameter[] */
    public $parameters = [];

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
