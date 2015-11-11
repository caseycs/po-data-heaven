<?php
namespace PODataHeaven\Exception;

class ColumnAlreadyExistsException extends PODataHeavenException
{
    public function __construct($column)
    {
        parent::__construct('Column already exists: ' . $column);
    }
}
