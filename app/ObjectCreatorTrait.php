<?php
namespace PODataHeaven;

use PODataHeaven\Exception\ClassNotFoundException;

trait ObjectCreatorTrait
{
    /**
     * @param string $prefix
     * @param string $middleName
     * @param string $suffix
     * @param array $parameters
     * @return mixed
     * @throws ClassNotFoundException
     */
    protected function newObjectByClassName($prefix, $middleName, $suffix, $parameters = [])
    {
        $className = '\\PODataHeaven\\' . trim($prefix, '\\') . '\\' . ucfirst($middleName) . $suffix;
        if (!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        return new $className($parameters);
    }
}
