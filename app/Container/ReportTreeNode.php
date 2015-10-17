<?php
namespace PODataHeaven\Container;

use Arrayzy\MutableArray;
use PODataHeaven\Model\Report;

class ReportTreeNode
{
    /** @var MutableArray|ReportTreeNode[] */
    public $children;

    /** @var MutableArray|Report[] */
    public $reports;

    public function __construct()
    {
        $this->children = new MutableArray();
        $this->reports = new MutableArray();
    }
}
