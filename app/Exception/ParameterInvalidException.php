<?php
namespace PODataHeaven\Exception;

use Exception;

class ParameterInvalidException extends PODataHeavenException
{
    public function __construct($parameterName, $parameterValue, Exception $prev = null)
    {
        parent::__construct(sprintf('Invalid parameter value: %s = %s', $parameterName, $parameterValue), 0, $prev);
    }
}
