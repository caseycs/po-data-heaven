<?php
namespace PODataHeaven\ReportTransformer;

interface TransformerInterface
{
    public function __construct(array $parameters = []);

    public function transform(array $row);
}
