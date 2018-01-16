<?php

namespace App\Basket\Entity;

use App\Shop\Entity\Product;

class BasketRow
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Product
     */
    private $product;
    /**
     * @var int
     */
    private $productId;
    /**
     * @var int
     */
    private $quantity = 1;
    
    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
    
    /**
     * @param Product $product
     * @return BasketRow
     */
    public function setProduct(Product $product): BasketRow
    {
        $this->product = $product;
        $this->setProductId($product->getId());
        return $this;
    }
    
    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
    
    /**
     * @param int $productId
     * @return BasketRow
     */
    public function setProductId(int $productId): BasketRow
    {
        $this->productId = $productId;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    
    /**
     * @param int $quantity
     * @return BasketRow
     */
    public function setQuantity(int $quantity): BasketRow
    {
        $this->quantity = $quantity;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): BasketRow
    {
        $this->id = $id;
        return $this;
    }
}
