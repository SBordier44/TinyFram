<?php

namespace Framework\Database;

class QueryResult implements \ArrayAccess, \Iterator
{
	/**
	 * @var array
	 */
	private $records;
	/**
	 * @var null|string
	 */
	private $entity;
	/**
	 * @var int Index
	 */
	private $index = 0;
	/**
	 * @var array
	 */
	private $hydratedRecords = [];
	
	/**
	 * QueryResult constructor.
	 * @param array       $records
	 * @param null|string $entity
	 */
	public function __construct(array $records, ?string $entity = null)
	{
		$this->records = $records;
		$this->entity  = $entity;
	}
	
	/**
	 * @return mixed|null|string
	 */
	public function current()
	{
		return $this->get($this->index);
	}
	
	/**
	 * @param int $index
	 * @return mixed|null|string
	 */
	public function get(int $index)
	{
		if ($this->entity) {
			if (!isset($this->hydratedRecords[$index])) {
				$this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
			}
			return $this->hydratedRecords[$index];
		}
		return $this->entity;
	}
	
	/**
	 * @return void
	 */
	public function next(): void
	{
		$this->index++;
	}
	
	/**
	 * @return int
	 */
	public function key(): int
	{
		return $this->index;
	}
	
	/**
	 * @return bool
	 */
	public function valid(): bool
	{
		return isset($this->records[$this->index]);
	}
	
	/**
	 * @return void
	 */
	public function rewind(): void
	{
		$this->index = 0;
	}
	
	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->records[$offset]);
	}
	
	/**
	 * @param mixed $offset
	 * @return mixed|null|string
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 * @throws \RuntimeException
	 */
	public function offsetSet($offset, $value)
	{
		throw new \RuntimeException("Can't alter records");
	}
	
	/**
	 * @param mixed $offset
	 * @throws \RuntimeException
	 */
	public function offsetUnset($offset)
	{
		throw new \RuntimeException("Can't alter records");
	}
	
	public function toArray(): array
	{
		$records = [];
		foreach ($this->records as $k => $v) {
			$records[] = $this->get($k);
		}
		return $records;
	}
}
