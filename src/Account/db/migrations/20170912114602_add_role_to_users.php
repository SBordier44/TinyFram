<?php

use Phinx\Migration\AbstractMigration;

class AddRoleToUsers extends AbstractMigration
{
	public function change()
	{
		$this->table('users')->addColumn('role', 'string', ['default' => 'user'])->update();
	}
}
