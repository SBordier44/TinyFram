<?php

use Phinx\Migration\AbstractMigration;

class AddResetToUsers extends AbstractMigration
{
	public function change()
	{
		$this->table('users')
			 ->addColumn('password_reset', 'string', ['null' => true])
			 ->addColumn('password_reset_at', 'datetime', ['null' => true])
			 ->update();
	}
}
