<?php

namespace App\Basket\Table;

use App\Basket\Entity\OrderRow;
use Framework\Database\Table;

class OrderRowTable extends Table
{
	/**
	 * @var string
	 */
	protected $table = 'orders_products';
	/**
	 * @var string
	 */
	protected $entity = OrderRow::class;
}
