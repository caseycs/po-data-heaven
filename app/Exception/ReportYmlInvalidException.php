<?php
namespace PODataHeaven\Exception;

class ReportYmlInvalidException extends PODataHeavenException
{
    public $report;

    public function __construct($report, $prev = null)
    {
        parent::__construct('Basic fields not presented for report: ' . $report, 0, $prev);
        $this->report = $report;
    }
}
