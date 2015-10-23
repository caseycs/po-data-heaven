<?php
namespace PODataHeaven\Exception;

class TransformerNotFoundException extends PODataHeavenException
{
    public $transformerName;

    public function __construct($transformerName)
    {
        parent::__construct('Report transformer not found: ' . $transformerName);
        $this->transformerName = $transformerName;
    }
}
