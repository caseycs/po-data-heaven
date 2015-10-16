<?php
namespace PODataHeaven\Exception;

use Exception;

class ReportParameterRequiredException extends Exception
{
    public $placeholder;

    public function __construct($placeholder)
    {
        parent::__construct('Report parameter required: ' . $placeholder);
        $this->placeholder = $placeholder;
    }
}
