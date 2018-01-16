<?php

use App\Shop\ShopWidget;
use Framework\Api\Stripe;
use function DI\add;
use function DI\get;
use function DI\object;

return [
    'admin.widgets' => add([
        get(ShopWidget::class)
    ]),
    Stripe::class   => object()->constructor(get('stripe.secret'))
];
