<?php
namespace PODataHeaven\Exception;

use Exception;

class DashboardInvalidException extends PODataHeavenException
{
    public function __construct($dashboard, Exception $prev = null)
    {
        parent::__construct(sprintf('Dashboard %s invalid', $dashboard), 0, $prev);
    }
}
