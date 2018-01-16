<?php

use Phinx\Migration\AbstractMigration;

class CreateBasketTable extends AbstractMigration
{
	public function change()
	{
		$constraints = [
			'delete' => 'cascade'
		];
		
		$this->table('baskets')
			 ->addColumn('user_id', 'integer')
			 ->addForeignKey('user_id', 'users', 'id', $constraints)
			 ->create();
		
		$this->table('baskets_products')
			 ->addColumn('basket_id', 'integer')
			 ->addColumn('product_id', 'integer')
			 ->addColumn('quantity', 'integer', ['default' => 1])
			 ->addForeignKey('basket_id', 'baskets', 'id', $constraints)
			 ->addForeignKey('product_id', 'products', 'id', $constraints)
			 ->create();
	}
}
