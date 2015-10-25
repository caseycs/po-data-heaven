<?php
namespace PODataHeaven\CellFormatter;

class MysqlDateFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return sprintf(
            '<span title="%s">%s</span>',
            $value,
            date($this->getParameter('format', 'd.m.y H:i'), strtotime($value))
        );
    }
}
