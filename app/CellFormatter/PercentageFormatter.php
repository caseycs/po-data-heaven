<?php
namespace PODataHeaven\CellFormatter;

class PercentageFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return round($value * 100, 2) . '%';
    }
}
