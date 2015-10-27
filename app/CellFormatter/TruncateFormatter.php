<?php
namespace PODataHeaven\CellFormatter;

use utilphp\util;

class TruncateFormatter extends AbstractFormatter
{
    public function format($value, array $row = [])
    {
        return $this->escape(util::limit_characters($value, $this->getParameter('limit', 20)));
    }
}
