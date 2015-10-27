<?php
namespace PODataHeaven\Collection;

use PODataHeaven\Exception\NonUniqueException;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Column;
use PODataHeaven\Model\Dashboard;

/* @method Dashboard first() */
/* @method Dashboard offsetGet($offset) */
class DashboardCollection extends Collection
{
    /**
     * @param string $name
     * @return Column
     * @throws NoResultException
     * @throws NonUniqueException
     */
    public function findOneByName($name)
    {
        return $this->findOne(function(Column $a) use ($name) {return $a->name === $name;});
    }

    /**
     * @param string $baseName
     * @return Dashboard
     * @throws NoResultException
     */
    public function findOneByBaseName($baseName)
    {
        $closure = function (Dashboard $d) use ($baseName) {
            return $d->baseName === $baseName;
        };
        return $this->findOne($closure);
    }
}
