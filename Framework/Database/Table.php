<?php

namespace Framework\Database;

class Table
{
    /**
     * @var null|\PDO
     */
    protected $pdo;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var string
     */
    protected $entity = \stdClass::class;
    
    /**
     * Table constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo->query("SELECT id, name FROM {$this->table}")->fetchAll(\PDO::FETCH_NUM);
        $list    = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }
    
    /**
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
    }
    
    /**
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))->from($this->table, $this->table[0])->into($this->entity);
    }
    
    /**
     * @param string $field
     * @param string $value
     * @return bool|mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->makeQuery()->where("$field = :field")->params(['field' => $value])->fetchOrFail();
    }
    
    /**
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }
    
    /**
     * @return int
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }
    
    /**
     * @param int   $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery   = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $query        = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $query->execute($params);
    }
    
    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params): string
    {
        return implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
    
    /**
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = implode(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = implode(', ', $fields);
        $query  = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $query->execute($params);
    }
    
    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $query->execute([$id]);
    }
    
    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }
    
    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
    
    /**
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $query = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchColumn() !== false;
    }
    
    /**
     * @return \PDO|null
     */
    public function getPdo(): ?\PDO
    {
        return $this->pdo;
    }
}
