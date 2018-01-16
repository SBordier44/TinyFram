<?php

namespace App\Blog\Entity;

use Framework\Entity\TimestampTrait;

class Post
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $slug;
    /**
     * @var string|null
     */
    private $content;
    /**
     * @var string|null
     */
    private $image;
    
    use TimestampTrait;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return Post
     */
    public function setId(int $id): Post
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    
    /**
     * @param string|null $name
     * @return Post
     */
    public function setName(?string $name): Post
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    /**
     * @param string|null $slug
     * @return Post
     */
    public function setSlug(?string $slug): Post
    {
        $this->slug = $slug;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }
    
    /**
     * @param string|null $content
     * @return Post
     */
    public function setContent(?string $content): Post
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
    
    /**
     * @param string|null $image
     * @return Post
     */
    public function setImage(?string $image): Post
    {
        $this->image = $image;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getThumb(): ?string
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }
    
    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return '/uploads/posts/' . $this->image;
    }
}
