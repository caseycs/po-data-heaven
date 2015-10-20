<?php
namespace PODataHeaven\CellFormatter;

use utilphp\util;

abstract class AbstractFormatter implements FormatterInterface
{
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    abstract public function format($value, array $row = []);

    final protected function getOption($key, $default)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    final protected function escape($value)
    {
        return util::htmlentities($value);
    }
}
