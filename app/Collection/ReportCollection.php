<?php
namespace PODataHeaven\Collection;

use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;

/* @method Report first() */
class ReportCollection extends Collection
{
    public function sortByNameAsc()
    {
//        return $this->customSort(
//            function (Report $a, Report $b) {
//                return strcmp($a->name, $b->name);
//            }
//        );
        return $this;
    }

    public function findOneByBaseName($baseName)
    {
        return $this->filter(
            function (Report $r) use ($baseName) {
                return $r->baseName === $baseName;
            }
        )->first();
    }

    public function findWithOnlyOneEntity(array $entities)
    {
        $result = [];
        /** @var Report $report */
        foreach ($this as $report) {
            if (!$report->parameters->count()) {
                continue;
            }
            if ($report->parameters->count() > 1) {
                continue;
            }
            /** @var Parameter $parameter */
            $parameter = $report->parameters->first();
            if (!in_array($parameter->idOfEntity, $entities, true)) {
                continue;
            }

            $tmp = new SearchByEntityResult;
            $tmp->report = $report;
            $tmp->parameter = $parameter;

            $result[] = $tmp;
        }
        return $result;
    }

    public function findWithEntityAndSomethingElse(array $entities)
    {
        $result = [];
        /** @var Report $report */
        foreach ($this as $report) {
            if (!$report->parameters->count()) {
                continue;
            }
            if ($report->parameters->count() < 2) {
                continue;
            }
            /** @var Parameter $parameter */
            foreach ($report->parameters as $parameter) {
                if (in_array($parameter->idOfEntity, $entities, true)) {
                    $tmp = new SearchByEntityResult;
                    $tmp->report = $report;
                    $tmp->parameter = $parameter;

                    $result[] = $tmp;
                    continue;
                }
            }
        }
        return $result;
    }
}
