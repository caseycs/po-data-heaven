<?php
namespace PODataHeaven\Exception;

class ColumnNotFoundException extends PODataHeavenException
{
    public function __construct($column)
    {
        parent::__construct('Required column not found: ' . $column);
    }
}
