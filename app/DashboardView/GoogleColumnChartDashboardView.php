<?php
namespace PODataHeaven\DashboardView;

use PODataHeaven\Container\ReportExecutionResult;

class GoogleColumnChartDashboardView extends DashboardViewAbstract implements DashboardViewInterface
{
    public function getTemplate()
    {
        return 'dashboard/googleColumnChart';
    }

    public function getTemplateData(ReportExecutionResult $reportExecutionResult)
    {
        $data = [$this->getRequiredParameter('stack')];
        $data[0][] = ['role' => 'annotation'];
        array_unshift($data[0], $this->getRequiredScalarParameter('barLegend'));

        $treatValuesAsPercentage = $this->hasParameter('treatValuesAsPercentage');

        foreach ($reportExecutionResult->rows as $row) {
            $tmp = [$row[$this->getRequiredScalarParameter('barLegend')]];

            foreach ($this->getRequiredArrayParameter('stack') as $key) {
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
