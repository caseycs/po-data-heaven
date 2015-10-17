<?php
namespace PODataHeaven\Controller;

use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Report;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ReportSafeFinderTrait
{
    /**
     * @param string $baseName
     * @return Report
     * @throws NotFoundHttpException
     */
    protected function findReport($baseName)
    {
        try {
            $reports = $this->reportParserService->getReportsTree();
            $report = $reports->findReport($baseName);
        } catch (NoResultException $e) {
            throw new NotFoundHttpException();
        }
        return $report;
    }
}
