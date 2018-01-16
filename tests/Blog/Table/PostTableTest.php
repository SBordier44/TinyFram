<?php

namespace Tests\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    /**
     * @var PostTable
     */
    private $postTable;
    
    public function setUp()
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }
    
    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        static::assertInstanceOf(Post::class, $post);
    }
    
    public function testFindNotFoundRecord()
    {
        $this->expectException(NoRecordException::class);
        $this->postTable->find(1);
    }
    
    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        /** @var Post $post */
        $post = $this->postTable->find(1);
        static::assertEquals('Salut', $post->getName());
        static::assertEquals('demo', $post->getSlug());
    }
    
    public function testInsert()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        /** @var Post $post */
        $post = $this->postTable->find(1);
        static::assertEquals('Salut', $post->getName());
        static::assertEquals('demo', $post->getSlug());
    }
    
    public function testDelete()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        static::assertEquals(2, (int)$count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        static::assertEquals(1, (int)$count);
    }
}
