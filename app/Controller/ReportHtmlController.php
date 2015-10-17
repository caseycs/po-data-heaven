<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportHtmlController
{
    /** @var Twig_Environment */
    var $twig;

    /** @var ReportParserService */
    var $reportParserService;

    /** @var ReportExecutorService */
    var $reportExecutorService;

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
        /** @var ReportCollection $reports */
        $reports = $this->reportParserService->getReportsTree()->reports;
        $report = $reports->findOneByBaseName($baseName);

        $reportExecutionResult = $this->reportExecutorService->execute($report, $request->query);

        $templateData = [
            'report' => $report,
            'result' => $reportExecutionResult,
            'csvUrl' => "/report/{$baseName}/csv?" . http_build_query($reportExecutionResult->parameters),
        ];

        return $this->twig->render('reportResult.twig', $templateData);
    }
}
