<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\ReportParserService;
use Twig_Environment;

class IndexController
{
    /** @var Twig_Environment */
    var $twig;

    /** @var ReportParserService */
    var $reportParserService;

    /**
     * IndexController constructor.
     * @param Twig_Environment $twig
     * @param ReportParserService $reportParserService
     */
    public function __construct(Twig_Environment $twig, ReportParserService $reportParserService)
    {
        $this->twig = $twig;
        $this->reportParserService = $reportParserService;
    }

    public function action()
    {
        $data = [
            'failedReports' => $this->reportParserService->getFailedReports(),
            'reports' => $this->reportParserService->getReportsTree()->reports,
        ];

        return $this->twig->render('index.twig', $data);
    }
}
