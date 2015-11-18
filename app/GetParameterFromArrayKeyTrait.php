<?php
namespace PODataHeaven;

use PODataHeaven\Exception\ParameterMissingException;
use PODataHeaven\Exception\ParameterNotArrayException;

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

    /**
     * @param array $data
     * @param string $key
     * @return array
     * @throws ParameterMissingException
     * @throws ParameterNotArrayException
     */
    protected function getRequiredArrayValue(array $data, $key)
    {
        if (!array_key_exists($key, $data)) {
            throw new ParameterMissingException($key);
        }
        if (!is_array($data[$key])) {
            throw new ParameterNotArrayException($key, $data[$key]);
        }
        return $data[$key];
    }
}
