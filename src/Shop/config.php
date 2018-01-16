<?php

use App\Shop\ShopWidget;
use Framework\Api\Stripe;
use function DI\{
	add, get, object
};

return [
	'admin.widgets' => add([
		get(ShopWidget::class)
	]),
	Stripe::class   => object()->constructor(get('stripe.secret'))
];
