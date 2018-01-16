<?php

namespace Framework\Event\Psr;

/**
 * Representation of an event
 */
interface EventInterface
{
	/**
	 * Get event name
	 * @return string
	 */
	public function getName(): string;
	
	/**
	 * Get target/context from which event was triggered
	 * @return mixed
	 */
	public function getTarget();
	
	/**
	 * Get parameters passed to the event
	 * @return array
	 */
	public function getParams(): array;
	
	/**
	 * Get a single parameter by name
	 * @param  string $name
	 * @return mixed
	 */
	public function getParam(string $name);
	
	/**
	 * Set the event name
	 * @param  string $name
	 * @return void
	 */
	public function setName(string $name): void;
	
	/**
	 * Set the event target
	 * @param  mixed $target
	 * @return void
	 */
	public function setTarget($target): void;
	
	/**
	 * Set event parameters
	 * @param  array $params
	 * @return void
	 */
	public function setParams(array $params): void;
	
	/**
	 * Indicate whether or not to stop propagating this event
	 * @param  bool $flag
	 */
	public function stopPropagation(bool $flag);
	
	/**
	 * Has this event indicated event propagation should stop?
	 * @return bool
	 */
	public function isPropagationStopped(): bool;
}
