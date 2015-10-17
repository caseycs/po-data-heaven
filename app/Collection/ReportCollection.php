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
        $this->walk(
            function (Report $r) use ($entities, &$result) {
                if ([] === $r->parameters) {
                    return;
                }
                if (count($r->parameters) > 1) {
                    return;
                }
                /** @var Parameter $parameter */
                $parameter = current($r->parameters);
                if (!in_array($parameter->idOfEntity, $entities, true)) {
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

    public function findWithEntityAndSomethingElse(array $entities)
    {
        $result = [];
        $this->walk(
            function (Report $r) use ($entities, &$result) {
                if ([] === $r->parameters) {
                    return;
                }
                if (count($r->parameters) < 2) {
                    return;
                }
                /** @var Parameter $parameter */
                foreach ($r->parameters as $parameter) {
                    if (in_array($parameter->idOfEntity, $entities, true)) {
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
