<?php
namespace PODataHeaven\CellFormatter;

interface FormatterInterface
{
    public function __construct(array $parameters = []);

    public function format($value, array $row = []);
}
