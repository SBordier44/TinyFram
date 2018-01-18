<?php

namespace Framework\Session;

use function array_key_exists;

class PHPSession implements SessionInterface
{
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
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
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
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
    public function offsetSet($offset, $value): mixed
    {
        return $this->set($offset, $value);
    }
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value): void
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }
    
    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
    
    /**
     * @param mixed $key
     * @return void
     */
    public function delete($key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }
    
    /**
     * @param mixed $key
     * @return bool
     */
    public function exists($key): bool
    {
        $this->ensureStarted();
        return array_key_exists($key, $_SESSION);
    }
}
