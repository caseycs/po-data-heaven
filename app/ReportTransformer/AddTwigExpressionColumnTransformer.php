<?php
namespace PODataHeaven\ReportTransformer;

use PODataHeaven\AbstractParameterContainer;
use PODataHeaven\Exception\ColumnNotFoundException;
use PODataHeaven\Exception\InvalidExpressionException;
use Twig_Error_Syntax;

class AddTwigExpressionColumnTransformer extends AbstractParameterContainer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(array $rows)
    {
        $expression = $this->getRequiredParameter('expression');
        $newColumnName = $this->getRequiredParameter('name');
        $beforeColumn = $this->getParameter('before');
        $afterColumn = $this->getParameter('after');

        $template = "{{ $expression }}";
        $twigLoader = new \Twig_Loader_Array(['column' => $template]);
        $twigEnvironment = new \Twig_Environment($twigLoader);

        if ($afterColumn) {
            $afterIndex = array_search($afterColumn, array_keys(reset($rows)), true);
            if (false === $afterIndex) {
                throw new ColumnNotFoundException($afterColumn);
            }
        } elseif ($beforeColumn) {
            $beforeIndex = array_search($beforeColumn, array_keys(reset($rows)), true);
            if (false === $beforeIndex) {
                throw new ColumnNotFoundException($beforeIndex);
            }
        }

        $result = [];
        foreach ($rows as $row) {
            try {
                $newColumnContent = $twigEnvironment->render('column', array_merge($row, ['_row' => $row]));
            } catch (Twig_Error_Syntax $e) {
                throw new InvalidExpressionException($e);
            }

            if ($afterColumn) {
                $before = array_slice($row, 0, $afterIndex + 1);
                $after = array_slice($row, $afterIndex + 1);
                $row = $before + [$newColumnName => $newColumnContent] + $after;
            } elseif ($beforeColumn) {
                $before = array_slice($row, 0, $beforeIndex);
                $after = array_slice($row, $beforeIndex);
                $row = $before + [$newColumnName => $newColumnContent] + $after;
            } else {
                $row[$newColumnName] = $newColumnContent;
            }

            $result[] = $row;
        }
        return $result;
    }
}
