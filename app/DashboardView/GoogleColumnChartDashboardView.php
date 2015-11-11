<?php
namespace PODataHeaven\DashboardView;

use PODataHeaven\Container\ReportExecutionResult;
use PODataHeaven\Exception\ColumnNotFoundException;

class GoogleColumnChartDashboardView extends DashboardViewAbstract implements DashboardViewInterface
{
    public function getTemplate()
    {
        return 'dashboard/googleColumnChart';
    }

    public function getTemplateData(ReportExecutionResult $reportExecutionResult)
    {
        $data = [$this->getRequiredParameter('stack')];
        $barLegend = $this->getRequiredScalarParameter('barLegend');

        $data[0][] = ['role' => 'annotation'];

        array_unshift($data[0], $barLegend);

        $treatValuesAsPercentage = $this->hasParameter('treatValuesAsPercentage');

        foreach ($reportExecutionResult->rows as $row) {
            if (!array_key_exists($barLegend, $row)) {
                throw new ColumnNotFoundException($barLegend);
            }

            $tmp = [$row[$barLegend]];

            foreach ($this->getRequiredArrayParameter('stack') as $key) {
                if (!array_key_exists($key, $row)) {
                    throw new ColumnNotFoundException($key);
                }

                $tmp[] = $treatValuesAsPercentage ? round((float)$row[$key] * 100, 2) : (float)$row[$key];
            }
            $tmp[] = '';

            $data[] = $tmp;
        }

        return [
            'data' => $data,
        ];
    }

    private function getHexColorBetweenGreenAndRed($valueFromZeroToOne)
    {
        $g = round(255 * $valueFromZeroToOne);
        $r = 255 - $g;
        return $this->rgb2hex([$r, $g, 0]);
    }

    private function rgb2hex($rgb)
    {
        $hex = "#";
        $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

        return $hex;
    }
}
