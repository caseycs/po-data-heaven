<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;

class AddTwigColumnTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $afterColumn = $this->getRequiredParameter('after');
        $template = $this->getRequiredParameter('template');
        $newColumnName = $this->getRequiredParameter('name');

        $twigLoader = new \Twig_Loader_Array(['column' => $template]);
        $twigEnvironment = new \Twig_Environment($twigLoader);

        $afterIndex = array_search($afterColumn, array_keys(reset($rows)), true);
        if (false === $afterIndex) {
            throw new ColumnNotFoundException($afterColumn);
        }

        $result = [];
        foreach ($rows as $row) {
            $newColumnContent = $twigEnvironment->render('column', ['row' => $row]);
            $before = array_slice($row, 0, $afterIndex + 1);
            $after = array_slice($row, $afterIndex + 1);
            $row = $before + [$newColumnName => $newColumnContent] + $after;

            $result[] = $row;
        }
        return $result;
    }
}
