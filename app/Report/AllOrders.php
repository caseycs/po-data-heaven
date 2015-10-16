<?php
namespace PODataHaven\Report;

class AllOrder
{
    const TITLE = '';
    const DESCRIPTION = '';

    public function getColumnsFormatting(ColumnsFormatter $formatter)
    {
        $formatter
            ->columnIsNumber('data')
        ;
    }

    public function getParameters(ParameterInjector $injector)
    {
        $injector->injectDate('dateFrom');
    }

    public function getQuery()
    {

    }
}