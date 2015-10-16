<?php
namespace PODataHeaven\Model;

class Column
{
    const FORMAT_RAW = 'raw';
    const FORMAT_NUMBER = 'number';

    /** @var string */
    public $name, $format, $idOfEntities;
}
