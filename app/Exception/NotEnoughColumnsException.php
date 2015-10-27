<?php
namespace PODataHeaven\Exception;

class NotEnoughColumnsException extends PODataHeavenException
{
    /**
     * @var int
     */
    public $columnsRequired;

    public function __construct($columnsRequired)
    {
        parent::__construct(sprintf('Required at least %s columns', $columnsRequired));
        $this->columnsRequired = $columnsRequired;
    }
}
