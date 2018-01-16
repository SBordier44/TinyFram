<?php

namespace Framework\Database;

use Pagerfanta\Pagerfanta;

class Query implements \IteratorAggregate
{
	/**
	 * @var array
	 */
	private $select = [];
	/**
	 * @var array
	 */
	private $from = [];
	/**
	 * @var array
	 */
	private $where = [];
	/**
	 * @var string
	 */
	private $entity;
	/**
	 * @var array
	 */
	private $order = [];
	/**
	 * @var string
	 */
	private $limit;
	/**
	 * @var array
	 */
	private $joins = [];
	/**
	 * @var null|\PDO
	 */
	private $pdo;
	/**
	 * @var array
	 */
	private $params = [];
	
	/**
	 * Query constructor.
	 * @param null|\PDO $pdo
	 */
	public function __construct(?\PDO $pdo = null)
	{
		$this->pdo = $pdo;
	}
	
	/**
	 * @param string      $table
	 * @param null|string $alias
	 * @return Query
	 */
	public function from(string $table, ?string $alias = null): self
	{
		if ($alias) {
			$this->from[$table] = $alias;
		} else {
			$this->from[] = $table;
		}
		return $this;
	}
	
	/**
	 * @param int $length
	 * @param int $offset
	 * @return Query
	 */
	public function limit(int $length, int $offset = 0): self
	{
		$this->limit = "$offset, $length";
		return $this;
	}
	
	/**
	 * @param string $order
	 * @return Query
	 */
	public function order(string $order): self
	{
		$this->order[] = $order;
		return $this;
	}
	
	/**
	 * @param string $table
	 * @param string $condition
	 * @param string $type
	 * @return Query
	 */
	public function join(string $table, string $condition, string $type = 'left'): self
	{
		$this->joins[$type][] = [$table, $condition];
		return $this;
	}
	
	/**
	 * @param string[] ...$condition
	 * @return Query
	 */
	public function where(string ...$condition): self
	{
		$this->where = array_merge($this->where, $condition);
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function count(): int
	{
		$query = clone $this;
		$table = current($this->from);
		return $query->select("COUNT($table.id)")->execute()->fetchColumn();
	}
	
	/**
	 * @return \PDOStatement
	 */
	private function execute(): \PDOStatement
	{
		$query = $this->__toString();
		if (!empty($this->params)) {
			$statement = $this->pdo->prepare($query);
			$statement->execute($this->params);
			return $statement;
		}
		return $this->pdo->query($query);
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$parts = ['SELECT'];
		if ($this->select) {
			$parts[] = implode(', ', $this->select);
		} else {
			$parts[] = '*';
		}
		$parts[] = 'FROM';
		$parts[] = $this->buildFrom();
		if (!empty($this->joins)) {
			/**
			 * @var string $type
			 * @var array  $joins
			 */
			foreach ($this->joins as $type => $joins) {
				foreach ($joins as [$table, $condition]) {
					$parts[] = strtoupper($type) . " JOIN $table ON $condition";
				}
			}
		}
		if (!empty($this->where)) {
			$parts[] = 'WHERE';
			$parts[] = '(' . implode(') AND (', $this->where) . ')';
		}
		if (!empty($this->order)) {
			$parts[] = 'ORDER BY';
			$parts[] = implode(', ', $this->order);
		}
		if ($this->limit) {
			$parts[] = 'LIMIT ' . $this->limit;
		}
		return implode(' ', $parts);
	}
	
	/**
	 * @return string
	 */
	private function buildFrom(): string
	{
		$from = [];
		foreach ($this->from as $key => $value) {
			if (\is_string($key)) {
				$from[] = "$key as $value";
			} else {
				$from[] = $value;
			}
		}
		return implode(', ', $from);
	}
	
	/**
	 * @param string[] ...$fields
	 * @return Query
	 */
	public function select(string ...$fields): self
	{
		$this->select = $fields;
		return $this;
	}
	
	/**
	 * @param array $params
	 * @return Query
	 */
	public function params(array $params): self
	{
		$this->params = array_merge($this->params, $params);
		return $this;
	}
	
	/**
	 * @param string $entity
	 * @return Query
	 */
	public function into(string $entity): self
	{
		$this->entity = $entity;
		return $this;
	}
	
	/**
	 * @return bool|mixed
	 * @throws NoRecordException
	 */
	public function fetchOrFail()
	{
		$record = $this->fetch();
		if ($record === false) {
			throw new NoRecordException('');
		}
		return $record;
	}
	
	/**
	 * @return bool|mixed
	 */
	public function fetch()
	{
		$record = $this->execute()->fetch(\PDO::FETCH_ASSOC);
		if ($record === false) {
			return false;
		}
		if ($this->entity) {
			return Hydrator::hydrate($record, $this->entity);
		}
		return $record;
	}
	
	/**
	 * @param int $columnNumber
	 * @return mixed
	 */
	public function fetchColumn(int $columnNumber = 0)
	{
		return $this->execute()->fetchColumn($columnNumber);
	}
	
	/**
	 * @param int $perPage
	 * @param int $currentPage
	 * @return Pagerfanta
	 * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
	 * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
	 * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
	 * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
	 * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
	 */
	public function paginate(int $perPage, int $currentPage = 1): Pagerfanta
	{
		$paginator = new PaginatedQuery($this);
		return (new Pagerfanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
	}
	
	/**
	 * @return QueryResult
	 */
	public function getIterator(): QueryResult
	{
		return $this->fetchAll();
	}
	
	/**
	 * @return QueryResult
	 */
	public function fetchAll(): QueryResult
	{
		return new QueryResult($this->execute()->fetchAll(\PDO::FETCH_ASSOC), $this->entity);
	}
}
