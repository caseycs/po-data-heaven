<?php
namespace PODataHeaven\CellFormatter;

class MinutesSecondsFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        $minutes = floor($value / 60);
        $seconds = $value % 60;
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
