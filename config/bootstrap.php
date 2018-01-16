<?php

use Framework\Mailer\SwiftMailerFactory;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\{
	RendererInterface, TwigRendererFactory
};
use Framework\Router\{
	Router, RouterFactory, RouterTwigExtension
};
use Framework\Session\{
	PHPSession, SessionInterface
};
use Framework\Twig\{
	CsrfExtension, FlashExtension, FormExtension, ModuleExtension, PagerFantaExtension, PriceExtension, TextExtension, TimeExtension
};
use Psr\Container\ContainerInterface;
use function DI\{
	factory, get, object
};

return [
	'env'                    => getenv('APP_ENV', 'dev'),
	'debug'                  => getenv('APP_ENV', 'dev') === 'dev',
	'views.path'             => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views',
	'twig.extensions'        => [
		get(RouterTwigExtension::class),
		get(PagerFantaExtension::class),
		get(TextExtension::class),
		get(TimeExtension::class),
		get(FlashExtension::class),
		get(FormExtension::class),
		get(CsrfExtension::class),
		get(ModuleExtension::class),
		get(PriceExtension::class)
	],
	SessionInterface::class  => object(PHPSession::class),
	CsrfMiddleware::class    => object()->constructor(get(SessionInterface::class)),
	Router::class            => factory(RouterFactory::class),
	RendererInterface::class => factory(TwigRendererFactory::class),
	PDO::class               => function (ContainerInterface $c) {
		return new PDO('mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name') . ';charset=UTF8',
			$c->get('database.username'), $c->get('database.password'), [
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
			]);
	},
	Swift_Mailer::class      => factory(SwiftMailerFactory::class)
];
