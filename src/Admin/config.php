<?php

use App\Admin\Action\DashboardAction;
use App\Admin\AdminModule;
use App\Admin\Twig\AdminTwigExtension;
use function DI\{
	get, object
};

return [
	'admin.prefix'            => '/admin',
	'admin.widgets'           => [],
	AdminTwigExtension::class => object()->constructor(get('admin.widgets')),
	AdminModule::class        => object()->constructorParameter('prefix', get('admin.prefix')),
	DashboardAction::class    => object()->constructorParameter('widgets', get('admin.widgets'))
];
