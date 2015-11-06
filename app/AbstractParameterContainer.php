<?php
namespace PODataHeaven;

use PODataHeaven\Exception\ParameterInvalidException;
use PODataHeaven\Exception\ParameterMissingException;

abstract class AbstractParameterContainer
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     * @return bool
     */
    final protected function hasParameter($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ParameterMissingException
     */
    final protected function getRequiredParameter($key)
    {
        if (!isset($this->parameters[$key])) {
            throw new ParameterMissingException($key);
        }
        return $this->parameters[$key];
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ParameterMissingException
     * @throws ParameterInvalidException
     */
    final protected function getRequiredScalarParameter($key)
    {
        $value = $this->getRequiredParameter($key);
        if (!is_scalar($value)) {
            throw new ParameterInvalidException($key, $value);
        }
        return $this->parameters[$key];
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ParameterMissingException
     * @throws ParameterInvalidException
     */
    final protected function getRequiredArrayParameter($key)
    {
        $value = $this->getRequiredParameter($key);
        if (!is_array($value)) {
            throw new ParameterInvalidException($key, $value);
        }
        return $this->parameters[$key];
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    final protected function getParameter($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }
}
