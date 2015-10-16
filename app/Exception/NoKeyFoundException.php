<?php
namespace PODataHeaven\Exception;

use Exception;

class NoKeyFoundException extends Exception
{
    public $key;

    public function __construct($key)
    {
        parent::__construct('Required key not found or empty: ' . $key);
        $this->key = $key;
    }
}
