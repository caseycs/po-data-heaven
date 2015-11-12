<?php
namespace PODataHeaven\CellFormatter;

class NumberFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return number_format($value, 2, '.', '');
    }
}
