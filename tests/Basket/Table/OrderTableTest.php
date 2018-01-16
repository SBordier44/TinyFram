<?php

namespace Tests\Basket\Table;

use App\Basket\Entity\Order;
use App\Basket\Helper\BasketHelper;
use App\Basket\Table\{
	OrderRowTable, OrderTable
};
use App\Shop\Table\ProductTable;
use Tests\DatabaseTestCase;

class OrderTableTest extends DatabaseTestCase
{
	/**
	 * @var OrderRowTable
	 */
	private $orderRowTable;
	/**
	 * @var OrderTable
	 */
	private $orderTable;
	/**
	 * @var ProductTable
	 */
	private $productTable;
	
	public function setUp()
	{
		$pdo = $this->getPDO();
		$this->migrateDatabase($pdo);
		$this->seedDatabase($pdo);
		$this->orderTable    = new OrderTable($pdo);
		$this->productTable  = new ProductTable($pdo);
		$this->orderRowTable = new OrderRowTable($pdo);
	}
	
	public function testFindRows()
	{
		$order = $this->testCreateFromBasket();
		$this->orderTable->findRows([$order]);
		self::assertCount(2, $order->getRows());
	}
	
	public function testCreateFromBasket()
	{
		$products = $this->productTable->makeQuery()->limit(10)->fetchAll();
		$basket   = new BasketHelper();
		$basket->addProduct($products[0]);
		$basket->addProduct($products[1], 2);
		$this->orderTable->createFromBasket($basket, [
			'country' => 'fr',
			'vat'     => 0,
			'user_id' => 1
		]);
		/** @var Order $order */
		$order = $this->orderTable->find(1);
		self::assertEquals($basket->getPrice(), $order->getPrice());
		self::assertEquals(2, $this->orderRowTable->count());
		return $order;
	}
}
