<?php
namespace PODataHeaven\Container;

use PODataHeaven\Collection\Collection;
use PODataHeaven\Collection\ReportCollection;

class ReportTreeNode
{
    /** @var Collection */
    public $children;

    /** @var ReportCollection */
    public $reports;

    public function __construct()
    {
        $this->children = new Collection();
        $this->reports = new ReportCollection();
    }
}
