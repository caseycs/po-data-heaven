<?php
namespace PODataHeaven\Container;

use PODataHeaven\Collection\Collection;
use PODataHeaven\Collection\ReportCollection;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Report;

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

    /**
     * @param string $baseName
     * @return Report
     * @throws NoResultException
     */
    public function findReport($baseName)
    {
        return $this->reports->findOneByBaseName($baseName);
    }
}
