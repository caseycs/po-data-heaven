<?php
namespace PODataHeaven\Exception;

class ReportParameterRequiredException extends PODataHeavenException
{
    public $placeholder;

    public function __construct($placeholder)
    {
        parent::__construct('Report parameter required: ' . $placeholder);
        $this->placeholder = $placeholder;
    }
}
