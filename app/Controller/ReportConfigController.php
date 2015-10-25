<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportConfigController
{
    use ReportSafeFinderTrait;

    /** @var Twig_Environment */
    protected $twig;

    /** @var ReportParserService */
    protected $reportParserService;

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
        $report = $this->findReport($baseName);

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
