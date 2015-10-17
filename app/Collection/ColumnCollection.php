<?php
namespace PODataHeaven\Collection;

use PODataHeaven\Exception\NonUniqueException;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Column;

class ColumnCollection extends Collection
{
    /**
     * @param string $name
     * @return Column
     * @throws NoResultException
     * @throws NonUniqueException
     */
    public function findByName($name)
    {
        return $this->findOne(function(Column $a) use ($name) {return $a->name === $name;});
    }
}
