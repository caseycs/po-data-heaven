<?php
namespace PODataHeaven\Exception;

use Exception;

class TransformerNotFoundException extends PODataHeavenException
{
    public function __construct($transformerName, Exception $prev = null)
    {
        parent::__construct(sprintf('Report transformer %s not found', $transformerName), 0, $prev);
    }
}
