<?php
namespace PODataHeaven\ReportTransformer;

use utilphp\util;

abstract class AbstractTransformer implements TransformerInterface
{
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    abstract public function transform(array $row);

    final protected function getOption($key, $default)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    final protected function escape($value)
    {
        return util::htmlentities($value);
    }
}
