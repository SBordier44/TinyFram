<?php

namespace Framework\Session;

interface SessionInterface
{
	
	/**
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get(string $key, $default = null);
	
	/**
	 * @param string $key
	 * @param        $value
	 * @return mixed
	 */
	public function set(string $key, $value): void;
	
	/**
	 * @param string $key
	 */
	public function delete(string $key): void;
}
