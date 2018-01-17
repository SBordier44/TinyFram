<?php

namespace Framework\Session;

class PHPSession implements SessionInterface, \ArrayAccess
{
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }
    
    /**
     * @return void
     */
    private function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
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
     * @param string $key
     * @param        $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }
    
    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
    
    /**
     * @param string $key
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
}
