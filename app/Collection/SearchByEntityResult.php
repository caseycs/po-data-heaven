<?php
namespace PODataHeaven\Collection;

use Arrayzy\ImmutableArray;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;

class SearchByEntityResult extends ImmutableArray
{
    /** @var Report */
    public $report;

    /** @var Parameter */
    public $parameter;
}
