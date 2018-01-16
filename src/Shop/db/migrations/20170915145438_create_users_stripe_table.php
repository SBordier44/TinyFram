<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersStripeTable extends AbstractMigration
{
    public function change()
    {
        $this->table('users_stripe')
             ->addColumn('user_id', 'integer')
             ->addForeignKey('user_id', 'users', 'id')
             ->addColumn('customer_id', 'string')
             ->addColumn('created_at', 'datetime')
             ->create();
    }
}
