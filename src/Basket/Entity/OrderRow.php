<?php

namespace App\Basket\Entity;

use App\Shop\Entity\Product;

class OrderRow
{
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var int|null
     */
    private $orderId;
    /**
     * @var int|null
     */
    private $productId;
    /**
     * @var float|null
     */
    private $price;
    /**
     * @var int|null
     */
    private $quantity;
    /**
     * @var Product|null
     */
    private $product;
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int|null $id
     * @return OrderRow
     */
    public function setId(?int $id): OrderRow
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getOrderId(): ?int
    {
        return $this->orderId;
    }
    
    /**
     * @param int|null $orderId
     * @return OrderRow
     */
    public function setOrderId(?int $orderId): OrderRow
    {
        $this->orderId = $orderId;
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }
    
    /**
     * @param int|null $productId
     * @return OrderRow
     */
    public function setProductId(?int $productId): OrderRow
    {
        $this->productId = $productId;
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
     * @param float|null $price
     * @return OrderRow
     */
    public function setPrice(?float $price): OrderRow
    {
        $this->price = $price;
        return $this;
    }
    
    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
    
    /**
     * @param int|null $quantity
     * @return OrderRow
     */
    public function setQuantity(?int $quantity): OrderRow
    {
        $this->quantity = $quantity;
        return $this;
    }
    
    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    
    /**
     * @param Product|null $product
     * @return OrderRow
     */
    public function setProduct(?Product $product): OrderRow
    {
        $this->product = $product;
        return $this;
    }
}
