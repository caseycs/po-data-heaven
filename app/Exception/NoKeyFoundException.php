<?php
namespace PODataHeaven\Exception;

class NoKeyFoundException extends PODataHeavenException
{
    public $key;

    public function __construct($key)
    {
        parent::__construct('Required key not found or empty: ' . $key);
        $this->key = $key;
    }
}
