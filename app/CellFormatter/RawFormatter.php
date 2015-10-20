<?php
namespace PODataHeaven\CellFormatter;

class RawFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return $this->escape($value);
    }
}
