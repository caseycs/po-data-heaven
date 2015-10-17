<?php
namespace PODataHeaven\Model;

class Column
{
    const FORMAT_RAW = 'raw';
    const FORMAT_NUMBER = 'number';

    /** @var string */
    public $name, $format = self::FORMAT_RAW;

    /** @var array */
    public $idOfEntities = [];

    /** @var string */
    public $chop;
}
