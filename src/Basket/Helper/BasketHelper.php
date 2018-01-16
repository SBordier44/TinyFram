<?php

namespace App\Basket\Helper;

use App\Basket\Entity\BasketRow;
use App\Shop\Entity\Product;
use function array_filter;
use function array_reduce;

class BasketHelper
{
    /**
     * @var BasketRow[]
     */
    protected $rows = [];
    
    /**
     * @param Product  $product
     * @param int|null $quantity
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        if ($quantity === 0) {
            $this->removeProduct($product);
        } else {
            $row = $this->getRow($product);
            if ($row === null) {
                $row = new BasketRow();
                $row->setProduct($product);
                $this->rows[] = $row;
            } else {
                $row->setQuantity($row->getQuantity() + 1);
            }
            if ($quantity !== null) {
                $row->setQuantity($quantity);
            }
        }
    }
    
    /**
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        $this->rows = array_filter($this->rows, function ($row) use ($product) {
            /** @var BasketRow $row */
            return $row->getProductId() !== $product->getId();
        });
    }
    
    /**
     * @param Product $product
     * @return BasketRow|null
     */
    protected function getRow(Product $product): ?BasketRow
    {
        /** @var BasketRow $row */
        foreach ($this->rows as $row) {
            if ($row->getProductId() === $product->getId()) {
                return $row;
            }
        }
        return null;
    }
    
    /**
     * @return int
     */
    public function count(): int
    {
        return array_reduce($this->rows, function ($count, BasketRow $row) {
            return $row->getQuantity() + $count;
        }, 0);
    }
    
    /**
     * @return BasketRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }
    
    /**
     * @param BasketRow[] $rows
     * @return BasketHelper
     */
    public function setRows(array $rows): BasketHelper
    {
        $this->rows = $rows;
        return $this;
    }
    
    /**
     * @return float
     */
    public function getPrice(): float
    {
        return array_reduce($this->rows, function ($total, BasketRow $row) {
            return $row->getQuantity() * $row->getProduct()->getPrice() + $total;
        }, 0);
    }
    
    /**
     * @return void
     */
    public function empty(): void
    {
        $this->rows = [];
    }
}
