<?php
namespace PODataHeaven\Exception;

use Exception;

class FormatterNotFoundException extends PODataHeavenException
{
    public function __construct($formatterOptionValue, Exception $prev = null)
    {
        parent::__construct('Formatter not found: ' . $formatterOptionValue, 0, $prev);
    }
}
