<?php

use App\Basket\Action\BasketAction;
use App\Basket\BasketFactory;
use App\Basket\Helper\BasketHelper;
use App\Basket\Twig\BasketTwigExtension;
use function DI\{
	add, factory, get, object
};

return [
	'twig.extensions'   => add([
		get(BasketTwigExtension::class)
	]),
	BasketHelper::class => factory(BasketFactory::class),
	BasketAction::class => object()->constructorParameter('stripeKey', get('stripe.key'))
];
