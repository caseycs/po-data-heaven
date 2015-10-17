<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportConfigController
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

    public function action($baseName, Request $request)
    {
        /** @var ReportCollection $reports */
        $reports = $this->reportParserService->getReportsTree()->reports;
        $report = $reports->findOneByBaseName($baseName);

        if (!$report->parameters->count()) {
            return new RedirectResponse("/report/{$baseName}/result");
        }

        return $this->twig->render(
            'reportConfig.twig',
            [
                'report' => $report,
                'parameters' => $request->query,
            ]
        );
    }
}
