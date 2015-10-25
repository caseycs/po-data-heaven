<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\DashboardParserService;
use PODataHeaven\Service\ReportParserService;
use Twig_Environment;

class IndexController
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var ReportParserService */
    protected $reportParserService;

    /** @var DashboardParserService */
    protected $dashboardParserService;

    /**
     * IndexController constructor.
     * @param Twig_Environment $twig
     * @param ReportParserService $reportParserService
     * @param DashboardParserService $dashboardParserService
     */
    public function __construct(
        Twig_Environment $twig,
        ReportParserService $reportParserService,
        DashboardParserService $dashboardParserService
    ) {
        $this->twig = $twig;
        $this->reportParserService = $reportParserService;
        $this->dashboardParserService = $dashboardParserService;
    }

    public function action()
    {
        $data = [
            'failedReports' => $this->reportParserService->getFailedReports(),
            'reports' => $this->reportParserService->getReportsTree()->reports,
            'dashboards' => $this->dashboardParserService->getDashboards(),
        ];

        return $this->twig->render('index.twig', $data);
    }
}
