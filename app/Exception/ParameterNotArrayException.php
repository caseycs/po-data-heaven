<?php
namespace PODataHeaven\Exception;

use Exception;

class ParameterNotArrayException extends PODataHeavenException
{
    public function __construct($parameterName, $parameterValue, Exception $prev = null)
    {
        parent::__construct(sprintf('Parameter value is not array: %s = %s', $parameterName, $parameterValue), 0, $prev);
    }
}
