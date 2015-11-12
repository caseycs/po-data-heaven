<?php
namespace PODataHeaven\Exception;

class UnknownDatabaseConnectionException extends PODataHeavenException
{
    public function __construct($connection)
    {
        parent::__construct('Unknown connection: ' . $connection);
    }
}
