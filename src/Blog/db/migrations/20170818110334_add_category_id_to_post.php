<?php

use Phinx\Migration\AbstractMigration;

class AddCategoryIdToPost extends AbstractMigration
{
	public function change()
	{
		$this->table('posts')
			 ->addColumn('category_id', 'integer', ['null' => true])
			 ->addForeignKey('category_id', 'categories', 'id', [
				 'delete' => 'SET NULL'
			 ])
			 ->update();
	}
}
