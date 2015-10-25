<?php
namespace PODataHeaven\Model;

use PODataHeaven\DashboardView\DashboardViewInterface;

class Dashboard
{
    /** @var string */
    public $filename;

    /** @var string */
    public $baseName;

    /** @var string */
    public $name;

    /** @var string */
    public $report;

    /** @var DashboardViewInterface */
    public $view;

    /** @var array */
    public $reportParameters;

    /** @var array */
    public $parameters;
}
