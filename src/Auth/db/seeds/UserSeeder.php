<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
	public function run(): void
	{
		$this->table('users')->insert([
			'firstname' => 'Administrator',
			'lastname'  => 'Admin',
			'username'  => 'admin',
			'role'      => 'admin',
			'email'     => 'admin@admin.fr',
			'password'  => password_hash('admin', PASSWORD_DEFAULT)
		])->save();
	}
}
