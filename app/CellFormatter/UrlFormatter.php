<?php
namespace PODataHeaven\CellFormatter;

use utilphp\util;

class UrlFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $value,
            $this->escape(util::limit_characters($value, $this->getParameter('limit', 20)))
        );
    }
}
