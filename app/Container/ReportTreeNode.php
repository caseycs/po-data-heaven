<?php
namespace PODataHeaven\Container;

use Arrayzy\MutableArray;
use PODataHeaven\Collection\ReportCollection;

class ReportTreeNode
{
    /** @var MutableArray|ReportTreeNode[] */
    public $children;

    /** @var ReportCollection */
    public $reports;

    public function __construct()
    {
        $this->children = new MutableArray();
        $this->reports = new ReportCollection();
    }
}
