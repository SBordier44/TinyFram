<?php

namespace App\Basket\Table;

use App\Auth\Entity\User;
use App\Basket\Entity\{
	BasketRow, Order, OrderRow
};
use App\Basket\Helper\BasketHelper;
use App\Shop\Entity\Product;
use Framework\Database\{
	Query, QueryResult, Table
};
use function implode;

class OrderTable extends Table
{
	/**
	 * @var string
	 */
	protected $table = 'orders';
	/**
	 * @var string
	 */
	protected $entity = Order::class;
	/**
	 * @var OrderRowTable
	 */
	private $orderRowTable;
	
	/**
	 * @param User $user
	 * @return Query
	 */
	public function findForUser(User $user): Query
	{
		return $this->makeQuery()->where("user_id = {$user->getId()}");
	}
	
	/**
	 * @param array $orders
	 * @return QueryResult
	 */
	public function findRows($orders): QueryResult
	{
		$ordersId = [];
		foreach ($orders as $order) {
			$ordersId[] = $order->getId();
		}
		$rows = $this->getRowTable()
					 ->makeQuery()
					 ->where('o.order_id IN (' . implode(',', $ordersId) . ')')
					 ->join('products as p', 'p.id = o.product_id')
					 ->select('o.*', 'p.name as productName', 'p.slug as productSlug')
					 ->fetchAll();
		/** @var OrderRow $row */
		foreach ($rows as $row) {
			foreach ($orders as $order) {
				if ($order->getId() === $row->getOrderId()) {
					$product = (new Product())->setId($row->getProductId())
											  ->setName($row->productName)
											  ->setSlug($row->productSlug);
					$row->setProduct($product);
					$order->addRow($row);
					break;
				}
			}
		}
		return $rows;
	}
	
	/**
	 * @return OrderRowTable
	 */
	private function getRowTable(): OrderRowTable
	{
		if ($this->orderRowTable === null) {
			$this->orderRowTable = new OrderRowTable($this->getPdo());
		}
		return $this->orderRowTable;
	}
	
	/**
	 * @param BasketHelper $basketHelper
	 * @param array        $params
	 */
	public function createFromBasket(BasketHelper $basketHelper, array $params = []): void
	{
		$params['price']      = $basketHelper->getPrice();
		$params['created_at'] = date('Y-m-d H:i:s');
		$this->pdo->beginTransaction();
		$this->insert($params);
		$orderId = $this->pdo->lastInsertId();
		/** @var BasketRow $row */
		foreach ($basketHelper->getRows() as $row) {
			$this->getRowTable()->insert([
				'order_id'   => $orderId,
				'price'      => $row->getProduct()->getPrice(),
				'product_id' => $row->getProductId(),
				'quantity'   => $row->getQuantity()
			]);
		}
		$this->pdo->commit();
	}
}
