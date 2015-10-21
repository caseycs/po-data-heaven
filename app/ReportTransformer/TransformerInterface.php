<?php
namespace PODataHeaven\ReportTransformer;

interface TransformerInterface
{
    public function __construct(array $options = []);

    public function transform(array $row);
}
