<?php
namespace PODataHeaven\Model;

use PODataHeaven\CellFormatter\FormatterInterface;

class Column
{
    /** @var string */
    public $name;

    /** @var FormatterInterface */
    public $formatter;
}
