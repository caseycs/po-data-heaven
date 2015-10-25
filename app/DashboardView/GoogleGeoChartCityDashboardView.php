<?php
namespace PODataHeaven\DashboardView;

use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Exception\NotEnoughColumnsException;

class GoogleGeoChartCityDashboardView extends DashboardViewAbstract implements DashboardViewInterface
{
    public function getTemplate()
    {
        return 'dashboard/googleGeoChartCity';
    }

    public function getTemplateData(ReportExecutionResult $reportExecutionResult)
    {
        $reportResultColumnsCount = count(reset($reportExecutionResult->rows));
        if ($reportResultColumnsCount < 2) {
            throw new NotEnoughColumnsException(2);
        }

        $columnsColumn = $reportResultColumnsCount > 2 ? 3 : 2;

        $chartData = [array_slice(array_keys(reset($reportExecutionResult->rows)), 0, $columnsColumn)];
        $chartData[0][0] = 'City';

        foreach ($reportExecutionResult->rows as $row) {
            if ($columnsColumn === 2) {
                $chartData[] = [array_shift($row), (float)array_shift($row)];
            } else {
                $chartData[] = [array_shift($row), (float)array_shift($row), (float)array_shift($row)];
            }
        }

        return [
            'rowsWithHeader' => $chartData,
            'region' => $this->getRequiredParameter('region'),
            'colorFrom' => $this->getRequiredParameter('colorFrom'),
            'colorTo' => $this->getRequiredParameter('colorTo'),
        ];
    }
}
