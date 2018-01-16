<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\Query;
use Framework\Database\Table;

class PostTable extends Table
{
    /**
     * @var string
     */
    protected $entity = Post::class;
    /**
     * @var string
     */
    protected $table = 'posts';
    
    /**
     * @param int $id
     * @return Query
     */
    public function findPublicForCategory(int $id): Query
    {
        return $this->findPublic()->where("p.category_id = $id");
    }
    
    /**
     * @return Query
     */
    public function findPublic(): Query
    {
        return $this->findAll()->where('p.published = 1')->where('p.created_at < NOW()');
    }
    
    /**
     * @return Query
     */
    public function findAll(): Query
    {
        $category = new CategoryTable($this->pdo);
        return $this->makeQuery()
                    ->join($category->getTable() . ' as c', 'c.id = p.category_id')
                    ->select('p.*, c.name as category_name, c.slug as category_slug')
                    ->order('p.created_at DESC');
    }
    
    /**
     * @param int $postId
     * @return Post
     */
    public function findWithCategory(int $postId): Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }
}
