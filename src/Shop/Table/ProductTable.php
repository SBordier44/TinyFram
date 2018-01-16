<?php

namespace App\Shop\Table;

use App\Shop\Entity\Product;
use Framework\Database\{
	Query, Table
};

class ProductTable extends Table
{
	/**
	 * @var string
	 */
	protected $entity = Product::class;
	/**
	 * @var string
	 */
	protected $table = 'products';
	
	/**
	 * @return Query
	 */
	public function findPublic(): Query
	{
		return $this->makeQuery()->where('created_at < NOW()');
	}
}
