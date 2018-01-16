<?php

use Phinx\Migration\AbstractMigration;

class AddCategoryTable extends AbstractMigration
{
	public function change()
	{
		$this->table('categories')
			 ->addColumn('name', 'string')
			 ->addColumn('slug', 'string')
			 ->addIndex('slug', ['unique' => true])
			 ->create();
	}
}
