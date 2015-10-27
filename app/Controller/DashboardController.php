<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\DashboardParserService;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Twig_Environment;

class DashboardController
{
    use ReportSafeFinderTrait;

    /** @var Twig_Environment */
    protected $twig;

    /** @var ReportParserService */
    protected $reportParserService;

    /** @var DashboardParserService */
    protected $dashboardParserService;

    /** @var ReportExecutorService */
    protected $reportExecutorService;

    /**
     * ReportConfigController constructor.
     * @param Twig_Environment $twig
     * @param ReportParserService $reportParserService
     * @param DashboardParserService $dashboardParserService
     * @param ReportExecutorService $reportExecutorService
     */
    public function __construct(
        Twig_Environment $twig,
        ReportParserService $reportParserService,
        DashboardParserService $dashboardParserService,
        ReportExecutorService $reportExecutorService
    ) {
        $this->twig = $twig;
        $this->reportParserService = $reportParserService;
        $this->dashboardParserService = $dashboardParserService;
        $this->reportExecutorService = $reportExecutorService;
    }

    public function action($baseName)
    {
        $dashboard = $this->dashboardParserService->findOneByBaseName($baseName);
        $report = $this->findReport($dashboard->report);

        $params = new ParameterBag($dashboard->reportParameters);
        $reportExecutionResult = $this->reportExecutorService->execute($report, $params);

        $templateData = $dashboard->view->getTemplateData($reportExecutionResult);

        //append predefined
        $templateData = array_merge($templateData, ['dashboard' => $dashboard, 'report' => $report]);

        return $this->twig->render($dashboard->view->getTemplate() . '.twig', $templateData);
    }
}
