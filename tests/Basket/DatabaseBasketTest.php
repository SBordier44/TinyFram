<?php

namespace Tests\Basket;

use App\Basket\Helper\BasketHelper;
use App\Basket\Helper\DatabaseBasketHelper;
use App\Basket\Table\BasketRowTable;
use App\Basket\Table\BasketTable;
use Tests\DatabaseTestCase;

class DatabaseBasketTest extends DatabaseTestCase
{
    /**
     * @var BasketRowTable
     */
    private $basketRowTable;
    /**
     * @var DatabaseBasketHelper
     */
    private $basket;
    /**
     * @var BasketTable
     */
    private $basketTable;
    
    public function setUp()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $this->basketTable    = new BasketTable($pdo);
        $this->basketRowTable = new BasketRowTable($pdo);
        $this->basket         = new DatabaseBasketHelper(2, $this->basketTable);
    }
    
    public function testAddProduct()
    {
        $products = $this->basketTable->getProductTable()->makeQuery()->limit(2)->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        self::assertEquals(2, $this->basketRowTable->count());
    }
    
    public function testPersistence()
    {
        $products = $this->basketTable->getProductTable()->makeQuery()->limit(2)->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $basket = new DatabaseBasketHelper(2, $this->basketTable);
        self::assertEquals(3, $basket->count());
    }
    
    public function testRemoveProduct()
    {
        $products = $this->basketTable->getProductTable()->makeQuery()->limit(2)->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $this->basket->removeProduct($products[1]);
        self::assertEquals(1, $this->basketRowTable->count());
    }
    
    public function testMergeBasket()
    {
        $products = $this->basketTable->getProductTable()->makeQuery()->limit(2)->fetchAll();
        $this->basket->addProduct($products[0]);
        $basket = new BasketHelper();
        $basket->addProduct($products[0], 2);
        $basket->addProduct($products[1]);
        $this->basket->merge($basket);
        self::assertEquals(4, $this->basket->count());
        self::assertEquals(3, $this->basket->getRows()[0]->getQuantity());
        self::assertEquals(1, $this->basket->getRows()[1]->getQuantity());
    }
    
    public function testEmptyBasket()
    {
        $products = $this->basketTable->getProductTable()->makeQuery()->limit(2)->fetchAll();
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[0]);
        $this->basket->addProduct($products[1], 2);
        $this->basket->empty();
        self::assertEquals(0, $this->basketRowTable->count());
    }
}
