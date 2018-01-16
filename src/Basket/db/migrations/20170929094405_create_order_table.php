<?php

use Phinx\Migration\AbstractMigration;

class CreateOrderTable extends AbstractMigration
{
    public function change()
    {
        $constraints = ['delete' => 'cascade'];
        $this->table('orders')
             ->addColumn('user_id', 'integer')
             ->addForeignKey('user_id', 'users', 'id')
             ->addColumn('price', 'float', ['precision' => 6, 'scale' => 2])
             ->addColumn('vat', 'float', ['precision' => 6, 'scale' => 2])
             ->addColumn('country', 'string')
             ->addColumn('created_at', 'datetime')
             ->addColumn('stripe_id', 'string')
             ->create();
        
        $this->table('orders_products')
             ->addColumn('order_id', 'integer')
             ->addColumn('product_id', 'integer')
             ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
             ->addColumn('quantity', 'integer', ['default' => 1])
             ->addForeignKey('order_id', 'orders', 'id', $constraints)
             ->addForeignKey('product_id', 'products', 'id', $constraints)
             ->create();
        
        // $this->table('purchases')->drop();
    }
}
