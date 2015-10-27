<?php
namespace PODataHeaven\DashboardView;

use PODataHeaven\Container\ReportExecutionResult;

class LeafletJsGeoDashboardView extends DashboardViewAbstract implements DashboardViewInterface
{
    public function getTemplate()
    {
        return 'dashboard/leafletJsGeo';
    }

    public function getTemplateData(ReportExecutionResult $reportExecutionResult)
    {
        $rows2js = [];

        $latKey = $this->getRequiredParameter('lat');
        $lonKey = $this->getRequiredParameter('lon');
        $sizeKey = $this->getRequiredParameter('size');
        $colorKey = $this->getRequiredParameter('color');
        $titleKey = $this->getRequiredParameter('title');

        $maxSize = 0;
//        $maxColor = 0;

        foreach ($reportExecutionResult->rows as $rowSource) {
//            if ($rowSource[$colorKey] > $maxColor) {
//                $maxColor = $rowSource[$colorKey];
//            }
            if (log10($rowSource[$sizeKey]) > $maxSize) {
                $maxSize = log10($rowSource[$sizeKey]);
            }
        }

        foreach ($reportExecutionResult->rows as $rowSource) {
            if (!$rowSource[$latKey] || !$rowSource[$lonKey]) {
                continue;
            }

            $rows2js[] = [
                'lat' => $rowSource[$latKey],
                'lon' => $rowSource[$lonKey],
                'sizeRaw' => $rowSource[$sizeKey],
                'size' => log10($rowSource[$sizeKey] / $maxSize),
                'colorRaw' => round($rowSource[$colorKey] * 100, 2) . '%',
                'color' => $this->getHexColorBetweenGreenAndRed($rowSource[$colorKey] / 1),
                'title' => $rowSource[$titleKey],
            ];
        }

        return [
            'rows' => $rows2js,
            'params' => $this->parameters,
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
