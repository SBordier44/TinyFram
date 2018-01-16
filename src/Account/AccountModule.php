<?php

namespace App\Account;

use App\Account\Action\AccountAction;
use App\Account\Action\AccountEditAction;
use App\Account\Action\SignupAction;
use Framework\Middleware\LoggedInMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;

class AccountModule extends Module
{
	public const MIGRATIONS  = __DIR__ . '/db/migrations';
	public const DEFINITIONS = __DIR__ . '/config.php';
	
	/**
	 * Constructor of AccountModule
	 * @param Router            $router
	 * @param RendererInterface $renderer
	 * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
	 */
	public function __construct(Router $router, RendererInterface $renderer)
	{
		$renderer->addPath('account', __DIR__ . '/views');
		$router->get('/signup', SignupAction::class, 'account.signup');
		$router->get('/account', [LoggedInMiddleware::class, AccountAction::class], 'account');
		$router->post('/account', [LoggedInMiddleware::class, AccountEditAction::class]);
		$router->post('/signup', SignupAction::class);
	}
}
