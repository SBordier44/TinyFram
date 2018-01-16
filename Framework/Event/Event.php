<?php

namespace Framework\Event;

use Framework\Event\Psr\EventInterface;

class Event implements EventInterface
{
	/**
	 * @var string
	 */
	protected $name = '';
	/**
	 * @var mixed
	 */
	private $target;
	/**
	 * @var array
	 */
	private $params = [];
	/**
	 * @var bool
	 */
	private $propagationStopped = false;
	
	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
	
	/**
	 * Set the event name
	 * @param  string $name
	 * @return void
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}
	
	/**
	 * @return mixed
	 */
	public function getTarget()
	{
		return $this->target;
	}
	
	/**
	 * Set the event target
	 * @param  mixed $target
	 * @return void
	 */
	public function setTarget($target): void
	{
		$this->target = $target;
	}
	
	/**
	 * @return array
	 */
	public function getParams(): array
	{
		return $this->params;
	}
	
	/**
	 * @param  array $params
	 * @return void
	 */
	public function setParams(array $params): void
	{
		$this->params = $params;
	}
	
	/**
	 * @param  string $name
	 * @return mixed
	 */
	public function getParam(string $name)
	{
		return $this->params[$name] ?? null;
	}
	
	/**
	 * @param  bool $flag
	 */
	public function stopPropagation(bool $flag): void
	{
		$this->propagationStopped = $flag;
	}
	
	/**
	 * @return bool
	 */
	public function isPropagationStopped(): bool
	{
		return $this->propagationStopped;
	}
}
