<?php

namespace App\Basket\Entity;

class Basket
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var int
     */
    private $productId;
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     * @return Basket
     */
    public function setId(int $id): Basket
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @param int $userId
     * @return Basket
     */
    public function setUserId(int $userId): Basket
    {
        $this->userId = $userId;
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
     * @return Basket
     */
    public function setProductId(int $productId): Basket
    {
        $this->productId = $productId;
        return $this;
    }
}
