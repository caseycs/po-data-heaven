<?php
namespace PODataHeaven\Exception;

use Exception;

class InvalidExpressionException extends PODataHeavenException
{
    public function __construct(Exception $prev)
    {
        parent::__construct(sprintf('Invalid expression: %s', $prev->getMessage()), 0, $prev);
    }
}
