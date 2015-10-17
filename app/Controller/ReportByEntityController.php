<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Service\ReportParserService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig_Environment;

class ReportByEntityController
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var ReportParserService */
    protected $reportParserService;

    /**
     * ReportConfigController constructor.
     * @param Twig_Environment $twig
     * @param ReportParserService $reportParserService
     */
    public function __construct(
        Twig_Environment $twig,
        ReportParserService $reportParserService
    ) {
        $this->twig = $twig;
        $this->reportParserService = $reportParserService;
    }

    public function action($entities, $entityId)
    {
        /** @var ReportCollection $reports */
        $reports = $this->reportParserService->getReportsTree()->reports;

        $entities = array_filter(explode(',', $entities));

        $reportsOnlyWithOnlyOneParameter = $reports->findWithOnlyOneEntity($entities);
        $reportsWithOtherParameters = $reports->findWithEntityAndSomethingElse($entities);

        if (count($reportsOnlyWithOnlyOneParameter) === 1 && count($reportsWithOtherParameters) === 0) {
            $first = reset($reportsOnlyWithOnlyOneParameter);
            $url = "/report/{$first->report->baseName}/result?{$first->parameter->placeholder}={$entityId}";
            return new RedirectResponse($url);
        }

        return $this->twig->render(
            'byEntityId.twig',
            [
                'entities' => $entities,
                'entityId' => $entityId,
                'reports' => [
                    'only' => $reportsOnlyWithOnlyOneParameter,
                    'rest' => $reportsWithOtherParameters,
                ]
            ]
        );
    }
}
