<?php
namespace PODataHeaven\CellFormatter;

class PercentageFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return number_format($value * 100, 2, '.', '') . '%';
    }
}
