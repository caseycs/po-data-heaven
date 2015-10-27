<?php
namespace PODataHeaven\DashboardView;

use PODataHeaven\Container\ReportExecutionResult;

interface DashboardViewInterface
{
    public function __construct(array $parameters = []);

    public function getTemplate();

    public function getTemplateData(ReportExecutionResult $reportExecutionResult);
}
