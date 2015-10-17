<?php
namespace PODataHeaven\Collection;

use PODataHeaven\Exception\NonUniqueException;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Model\Column;

/* @method Column first() */
/* @method Column offsetGet($offset) */
class ColumnCollection extends Collection
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
}
