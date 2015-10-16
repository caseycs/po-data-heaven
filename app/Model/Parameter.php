<?php
namespace PODataHeaven\Model;

class Parameter
{
    const INPUT_RAW = 'raw';
    const INPUT_DATE = 'date';
    const INPUT_DATETIME = 'datetime';

    /** @var string */
    public $name, $type, $idOfEntity, $default;

    public function getDefault()
    {
        $value = strtolower($this->default);

        switch ($value) {
            case 'today':
                return 'Y-m-d';
            case 'now':
                return 'Y-m-d H:i:s';
            case 'todaystart':
                return 'Y-m-d H:i:s';
            case 'todayfinish':
                return 'Y-m-d H:i:s';
            case 'weekstart':
                return 'Y-m-d H:i:s';
            case 'weekfinish':
                return 'Y-m-d H:i:s';
            case 'monthstart':
                return 'Y-m-d H:i:s';
            case 'monthfinish':
                return 'Y-m-d H:i:s';
            case 'yearstart':
                return 'Y-m-d H:i:s';
            case 'yearfinish':
                return 'Y-m-d H:i:s';
            default:
                return $value;
        }
    }
}
