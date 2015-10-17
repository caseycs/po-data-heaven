<?php
namespace PODataHeaven\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use PODataHeaven\Exception\NoResultException;
use PODataHeaven\Exception\NonUniqueException;

class Collection extends ArrayCollection
{
    public function findOne(Closure $filter)
    {
        $found = $this->filter($filter);
        if (!$found->count()) {
            throw new NoResultException;
        } elseif ($found->count() > 1) {
            throw new NonUniqueException;
        }
        return $found->first();
    }
}
