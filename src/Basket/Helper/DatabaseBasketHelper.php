<?php

namespace App\Basket\Helper;

use App\Basket\Entity\Basket;
use App\Basket\Table\BasketTable;
use App\Shop\Entity\Product;

class DatabaseBasketHelper extends BasketHelper
{
    /**
     * @var int
     */
    private $userId;
    /**
     * @var BasketTable
     */
    private $basketTable;
    /**
     * @var Basket|null
     */
    private $basketEntity;
    
    /**
     * DatabaseBasketHelper constructor.
     * @param int         $userId
     * @param BasketTable $basketTable
     */
    public function __construct(int $userId, BasketTable $basketTable)
    {
        $this->userId       = $userId;
        $this->basketTable  = $basketTable;
        $this->basketEntity = $this->basketTable->findForUser($userId);
        if ($this->basketEntity) {
            $this->rows = $this->basketTable->findRows($this->basketEntity);
        }
    }
    
    /**
     * @param BasketHelper $basket
     */
    public function merge(BasketHelper $basket)
    {
        $rows = $basket->getRows();
        foreach ($rows as $r) {
            $row = $this->getRow($r->getProduct());
            if ($row) {
                $this->addProduct($r->getProduct(), $row->getQuantity() + $r->getQuantity());
            } else {
                $this->addProduct($r->getProduct(), $r->getQuantity());
            }
        }
    }
    
    /**
     * @param Product  $product
     * @param int|null $quantity
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        if ($this->basketEntity === null) {
            $this->basketEntity = $this->basketTable->createForUser($this->userId);
        }
        if ($quantity === 0) {
            $this->removeProduct($product);
        } else {
            $row = $this->getRow($product);
            if ($row === null) {
                $this->rows[] = $this->basketTable->addRow($this->basketEntity, $product, $quantity ? : 1);
            } else {
                $this->basketTable->updateRowQuantity($row, $quantity ? : ($row->getQuantity() + 1));
            }
        }
    }
    
    /**
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        $row = $this->getRow($product);
        if ($row !== null) {
            $this->basketTable->deleteRow($row);
            parent::removeProduct($product);
        }
    }
    
    /**
     * @return void
     */
    public function empty(): void
    {
        $this->basketTable->deleteRows($this->basketEntity);
        parent::empty();
    }
}
