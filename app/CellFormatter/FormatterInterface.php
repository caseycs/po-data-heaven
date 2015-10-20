<?php
namespace PODataHeaven\CellFormatter;

interface FormatterInterface
{
    public function __construct(array $options = []);

    public function format($value, array $row = []);
}
