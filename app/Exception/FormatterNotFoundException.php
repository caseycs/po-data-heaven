<?php
namespace PODataHeaven\Exception;

class FormatterNotFoundException extends PODataHeavenException
{
    public $formatterOptionValue;

    public function __construct($formatterOptionValue)
    {
        parent::__construct('Formatter not found: ' . $formatterOptionValue);
        $this->formatterOptionValue = $formatterOptionValue;
    }
}
