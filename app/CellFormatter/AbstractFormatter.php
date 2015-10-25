<?php
namespace PODataHeaven\CellFormatter;

use PODataHeaven\AbstractParameterContainer;
use utilphp\util;

abstract class AbstractFormatter extends AbstractParameterContainer implements FormatterInterface
{
    abstract public function format($value, array $row = []);

    final protected function escape($value)
    {
        return util::htmlentities($value);
    }
}
