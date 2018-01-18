<?php

namespace Framework\Session;

use ArrayAccess;

interface SessionInterface extends ArrayAccess
{
    
    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value): void;
    
    /**
     * @param mixed $key
     * @return void
     */
    public function delete($key): void;
}
