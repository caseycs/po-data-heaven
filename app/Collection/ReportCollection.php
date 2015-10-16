<?php
namespace PODataHeaven\Collection;

use Arrayzy\ImmutableArray;
use PODataHeaven\Model\Parameter;
use PODataHeaven\Model\Report;

class ReportCollection extends ImmutableArray
{
    public function sortByNameAsc()
    {
        return $this->customSort(
            function (Report $a, Report $b) {
                return strcmp($a->name, $b->name);
            }
        );
    }

    public function findOneByBaseName($baseName)
    {
        return $this->filter(
            function (Report $r) use ($baseName) {
                return $r->baseName === $baseName;
            }
        )->first();
    }

    public function findWithOnlyEntity($entity)
    {
        $result = [];
        $this->walk(
            function (Report $r) use ($entity, &$result) {
                if ([] === $r->parameters) {
                    return;
                }
                if (count($r->parameters) > 1) {
                    return;
                }
                /** @var Parameter $parameter */
                $parameter = current($r->parameters);
                if ($parameter->idOfEntity !== $entity) {
                    return;
                }

                $tmp = new SearchByEntityResult;
                $tmp->report = $r;
                $tmp->parameter = $parameter;

                $result[] = $tmp;
            }
        );
        return $result;
    }

    public function findWithEntityAndSomethingElse($entity)
    {
        $result = [];
        $this->walk(
            function (Report $r) use ($entity, &$result) {
                if ([] === $r->parameters) {
                    return;
                }
                if (count($r->parameters) < 2) {
                    return;
                }
                /** @var Parameter $parameter */
                foreach ($r->parameters as $parameter) {
                    if ($parameter->idOfEntity === $entity) {
                        $tmp = new SearchByEntityResult;
                        $tmp->report = $r;
                        $tmp->parameter = $parameter;

                        $result[] = $tmp;
                        return;
                    }
                }
            }
        );
        return $result;
    }
}
