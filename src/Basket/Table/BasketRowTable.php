<?php

namespace App\Basket\Table;

use App\Basket\Entity\BasketRow;
use Framework\Database\Table;

class BasketRowTable extends Table
{
    /**
     * @var string
     */
    protected $table = 'baskets_products';
    /**
     * @var string
     */
    protected $entity = BasketRow::class;
}
