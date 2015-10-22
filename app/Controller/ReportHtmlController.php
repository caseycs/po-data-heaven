<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportHtmlController
{
    use ReportSafeFinderTrait;

    /** @var Twig_Environment */
    protected $twig;

    /** @var ReportParserService */
    protected $reportParserService;

    /** @var ReportExecutorService */
    protected $reportExecutorService;

    /**
     * ReportConfigController constructor.
     * @param Twig_Environment $twig
     * @param ReportParserService $reportParserService
     * @param ReportExecutorService $reportExecutorService
     */
    public function __construct(
        Twig_Environment $twig,
        ReportParserService $reportParserService,
        ReportExecutorService $reportExecutorService
    ) {
        $this->twig = $twig;
        $this->reportParserService = $reportParserService;
        $this->reportExecutorService = $reportExecutorService;
    }

    public function action($baseName, Request $request)
    {
        $report = $this->findReport($baseName);

        $params = $request->isMethod('GET') ? $request->query : $request->request;
        $reportExecutionResult = $this->reportExecutorService->execute($report, $params);

        $templateData = [
            'report' => $report,
            'result' => $reportExecutionResult,
            'csvUrl' => "/report/{$baseName}/csv?" . http_build_query($reportExecutionResult->parameters),
        ];

        return $this->twig->render('reportResult.twig', $templateData);
    }
}
