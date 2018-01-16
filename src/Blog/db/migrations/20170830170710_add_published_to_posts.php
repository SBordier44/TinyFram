<?php

use Phinx\Migration\AbstractMigration;

class AddPublishedToPosts extends AbstractMigration
{
	public function change()
	{
		
		$this->table('posts')->addColumn('published', 'boolean', ['default' => false])->update();
	}
}
