<?php

namespace App\Shop\Entity;

use Framework\Entity\TimestampTrait;
use function is_string;
use function pathinfo;

class Product
{
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string|null
     */
    private $slug;
    /**
     * @var float|null|string
     */
    private $price;
    /**
     * @var string|null
     */
    private $image;
    
    use TimestampTrait;
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return Product
     */
    public function setId(int $id): Product
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
     * @return Product
     */
    public function setName(?string $name): Product
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    /**
     * @param string|null $description
     * @return Product
     */
    public function setDescription(?string $description): Product
    {
        $this->description = $description;
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
     * @return Product
     */
    public function setSlug(?string $slug): Product
    {
        $this->slug = $slug;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }
    
    /**
     * @param float|null|string $price
     * @return Product
     */
    public function setPrice($price): Product
    {
        if (is_string($price)) {
            $price = (float)$price;
        }
        $this->price = $price;
        return $this;
    }
    
    /**
     * @return string|
     */
    public function getThumb(): ?string
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/products/' . $filename . '_thumb.' . $extension;
    }
    
    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return '/uploads/products/' . $this->image;
    }
    
    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
    
    /**
     * @param string $image
     * @return Product
     */
    public function setImage(?string $image): Product
    {
        $this->image = $image;
        return $this;
    }
}
