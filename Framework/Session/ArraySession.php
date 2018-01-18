<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{
    /**
     * @var array
     */
    private $session = [];
    
    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        }
        return $default;
    }
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value): void
    {
        $this->session[$key] = $value;
    }
    
    /**
     * @param mixed $key
     */
    public function delete($key): void
    {
        if (array_key_exists($key, $this->session)) {
            unset($this->session[$key]);
        }
    }
    
    /**
     * @param mixed $key
     * @return bool
     */
    public function exists($key): bool
    {
        return array_key_exists($key, $this->session);
    }
    
    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }
    
    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }
    
    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}
