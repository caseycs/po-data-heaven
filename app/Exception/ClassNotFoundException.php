<?php
namespace PODataHeaven\Exception;

class ClassNotFoundException extends PODataHeavenException
{
    public function __construct($className)
    {
        parent::__construct(sprintf('Class %s not found', $className));
    }
}
