<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ReportJsonController
{
    use ReportSafeFinderTrait;

    /** @var ReportParserService */
    protected $reportParserService;

    /** @var ReportExecutorService */
    protected $reportExecutorService;

    /**
     * ReportConfigController constructor.
     * @param ReportParserService $reportParserService
     * @param ReportExecutorService $reportExecutorService
     */
    public function __construct(
        ReportParserService $reportParserService,
        ReportExecutorService $reportExecutorService
    ) {
        $this->reportParserService = $reportParserService;
        $this->reportExecutorService = $reportExecutorService;
    }

    public function action($baseName, Request $request)
    {
        $report = $this->findReport($baseName);

        $reportExecutionResult = $this->reportExecutorService->execute($report, $request->query);

        return new JsonResponse($reportExecutionResult->rows);
    }
}
