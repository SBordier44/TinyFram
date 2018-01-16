<?php

use Phinx\Migration\AbstractMigration;

class AddNameToUsers extends AbstractMigration
{
	public function change()
	{
		$this->table('users')->addColumn('firstname', 'string')->addColumn('lastname', 'string')->update();
	}
}
