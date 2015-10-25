<?php
namespace PODataHeaven\Exception;

class ParameterEmptyException extends PODataHeavenException
{
    /**
     * @var string
     */
    public $parameter;

    public function __construct($parameter)
    {
        parent::__construct('Parameter empty: ' . $parameter);
        $this->parameter = $parameter;
    }
}
