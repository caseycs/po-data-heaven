<?php
namespace PODataHeaven;

use PODataHeaven\Exception\ParameterMissingException;

trait GetParameterFromArrayKeyTrait
{
    /**
     * @param array $data
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getValue(array $data, $key, $default = null)
    {
        return isset($data[$key]) ? $data[$key] : $default;
    }

    /**
     * @param array $data
     * @param string $key
     * @return bool
     */
    protected function hasValue(array $data, $key)
    {
        return isset($data[$key]);
    }

    /**
     * @param array $data
     * @param string $key
     * @return mixed
     * @throws ParameterMissingException
     */
    protected function getRequiredValue(array $data, $key)
    {
        if (!isset($data[$key]) || '' === trim($data[$key])) {
            throw new ParameterMissingException($key);
        }
        return $data[$key];
    }
}
