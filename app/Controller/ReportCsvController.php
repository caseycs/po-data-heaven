<?php
namespace PODataHeaven\Controller;

use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Service\ReportExecutorService;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;

class ReportCsvController
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
