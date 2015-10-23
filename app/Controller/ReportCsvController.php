<?php
namespace PODataHeaven\Controller;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportCsvController
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

        $reportExecutionResult = $this->reportExecutorService->execute($report, $request->query);

        //hack for Goodby\CSV
        error_reporting(~E_STRICT);

        $config = new ExporterConfig();
        $exporter = new Exporter($config);

        $date = date('m-d_h-i');
        \utilphp\util::force_download("{$baseName}-{$date}.csv");

        $rows2export = $reportExecutionResult->rows;
        array_unshift($rows2export, array_keys($rows2export[0]));

        $exporter->export('php://output', $rows2export);

        return '';
    }
}
