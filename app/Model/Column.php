<?php
namespace PODataHeaven\Model;

use PODataHeaven\CellFormatter\FormatterInterface;

class Column
{
    const ALIGN_LEFT = 'left';
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';

    /** @var string */
    public $name;

    /** @var FormatterInterface */
    public $formatter;

    /** @var string */
    public $align = self::ALIGN_LEFT;
}
