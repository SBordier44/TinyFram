<?php

namespace Framework\Event\Psr;

/**
 * Interface for EventManager
 */
interface EventManagerInterface
{
	/**
	 * @param string   $event
	 * @param callable $callback
	 * @param int      $priority
	 * @return bool
	 */
	public function attach(string $event, callable $callback, int $priority = 0): bool;
	
	/**
	 * @param string   $event
	 * @param callable $callback
	 * @return bool
	 */
	public function detach(string $event, callable $callback): bool;
	
	/**
	 * @param  string $event
	 * @return void
	 */
	public function clearListeners(string $event): void;
	
	/**
	 * @param  string|EventInterface $event
	 * @param  mixed                 $target
	 * @param  array                 $argv
	 * @return mixed
	 */
	public function trigger($event, $target = null, array $argv = []);
}
