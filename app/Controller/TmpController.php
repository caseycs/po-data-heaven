<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Service\ReportResultStorageService;

class TmpController
{
    /** @var ReportResultStorageService */
    protected $reportResultStorageService;

    /**
     * @param ReportResultStorageService $service
     */
    public function __construct(ReportResultStorageService $service) {
        $this->reportResultStorageService = $service;
    }

    public function action()
    {
        $report = new ReportExecutionResult();
        $report->rows = [
            ['a' => '1', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
            ['a' => '2', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
            ['a' => '3', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
            ['a' => '4', 'c' => '555', 'd' => null, 'e' => '5.0', 'f' => null],
            ['a' => '5', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
            ['a' => '6', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
            ['a' => '7', 'c' => '555', 'd' => '55.54', 'e' => '5.0', 'f' => null],
        ];

        $this->reportResultStorageService->store($report, '_report');
    }
}
