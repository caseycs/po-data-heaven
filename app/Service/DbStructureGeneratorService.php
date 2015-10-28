<?php
namespace PODataHeaven\Service;

use Doctrine\DBAL\Schema\Table;
use utilphp\util;

class DbStructureGeneratorService
{
    public function guessColumnTypes(array $rows, Table $tableSchema)
    {
        $columns = [];
        foreach (array_keys($rows[0]) as $name) {
            $columns[$name] = [
                'null' => false,
                'numeric' => true,
                'max_int_numbers' => 0,
                'max_decimal_numbers' => 0,
                'max_length' => 0
            ];
        }

        //analyze result rows values
        foreach ($rows as $row) {
            foreach ($row as $name => $value) {
                if (null === $value) {
                    $columns[$name]['null'] = true;
                    continue;
                }

                $columns[$name]['numeric'] = $columns[$name]['numeric'] && (string)(float)$value === (string)$value;

                if ($columns[$name]['numeric']) {
                    if ($columns[$name]['max_int_numbers'] < strlen((int)$value)) {
                        $columns[$name]['max_int_numbers'] = strlen((int)$value);
                    }

                    $decimals = strlen(abs($value) - floor(abs($value))) - 2;
                    if ($columns[$name]['max_decimal_numbers'] < $decimals) {
                        $columns[$name]['max_decimal_numbers'] = $decimals;
                    }
                }

                if ($columns[$name]['max_length'] <= strlen($value)) {
                    $columns[$name]['max_length'] = strlen($value);
                }
            }
        }

        //generate table structure
        foreach ($columns as $name => $data) {
            if ($data['numeric'] && !$data['max_decimal_numbers']) {
                $tableSchema->addColumn($this->normalizeColumnName($name), 'integer', ['notnull' => !$data['null']]);
            } elseif ($data['numeric'] && $data['max_decimal_numbers']) {
                $properties = [
                    'precision' => $data['max_int_numbers'] + $data['max_decimal_numbers'],
                    'scale' => $data['max_decimal_numbers'],
                    'notnull' => !$data['null']
                ];
                $tableSchema->addColumn($this->normalizeColumnName($name), 'decimal', $properties);
            } else {
                $tableSchema->addColumn(
                    $this->normalizeColumnName($name),
                    'string',
                    ['length' => $data['max_length'], 'notnull' => !$data['null']]
                );
            }
        }
    }

    /**
     * @param string $column
     * @return mixed
     */
    private function normalizeColumnName($column)
    {
        return str_replace(' ', '', lcfirst(ucwords(util::slugify($column, ' '))));
    }
}
