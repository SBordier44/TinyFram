<?php

namespace Framework\Event;

use Framework\Event\Psr\{
	EventInterface, EventManagerInterface
};

class EventManager implements EventManagerInterface
{
	/**
	 * @var array
	 */
	private $listeners = [];
	
	/**
	 * @param string   $event
	 * @param callable $callback
	 * @param int      $priority
	 * @return bool
	 */
	public function attach(string $event, callable $callback, int $priority = 0): bool
	{
		$this->listeners[$event][] = [
			'callback' => $callback,
			'priority' => $priority
		];
		return true;
	}
	
	/**
	 * @param string   $event
	 * @param callable $callback
	 * @return bool
	 */
	public function detach(string $event, callable $callback): bool
	{
		$this->listeners[$event] = array_filter($this->listeners[$event], function ($listener) use ($callback) {
			return $listener['callback'] !== $callback;
		});
		return true;
	}
	
	/**
	 * @param  string $event
	 * @return void
	 */
	public function clearListeners(string $event): void
	{
		$this->listeners[$event] = [];
	}
	
	/**
	 * @param  string|EventInterface $event
	 * @param  mixed                 $target
	 * @param  array                 $argv
	 */
	public function trigger($event, $target = null, array $argv = [])
	{
		if (\is_string($event)) {
			$event = $this->makeEvent($event, $target, $argv);
		}
		if (isset($this->listeners[$event->getName()])) {
			$listeners = $this->listeners[$event->getName()];
			usort($listeners, function ($listenerA, $listenerB) {
				return $listenerB['priority'] - $listenerA['priority'];
			});
			/** @var array $listeners */
			foreach ($listeners as ['callback' => $callback]) {
				if ($event->isPropagationStopped()) {
					break;
				}
				$callback($event);
			}
		}
	}
	
	private function makeEvent(string $eventName, $target = null, array $argv = []): EventInterface
	{
		$event = new Event();
		$event->setName($eventName);
		$event->setTarget($target);
		$event->setParams($argv);
		return $event;
	}
}
