<?php

use Phinx\Seed\AbstractSeed;

class ProductSeeder extends AbstractSeed
{
	public function run(): void
	{
		$faker = \Faker\Factory::create('fr_FR');
		
		$data = [];
		for ($i = 0; $i < 100; ++$i) {
			$date   = $faker->unixTime();
			$data[] = [
				'name'        => $faker->catchPhrase,
				'slug'        => $faker->slug,
				'description' => $faker->text(3000),
				'image'       => 'fake.jpg',
				'created_at'  => date('Y-m-d H:i:s', $date),
				'updated_at'  => date('Y-m-d H:i:s', $date),
				'price'       => $faker->numberBetween(10, 1000)
			];
		}
		$this->table('products')->insert($data)->save();
	}
}
