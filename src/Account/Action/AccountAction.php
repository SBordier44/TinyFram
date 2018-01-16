<?php

namespace App\Account\Action;

use Framework\Auth\AuthInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class AccountAction
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var AuthInterface
	 */
	private $auth;
	
	/**
	 * AccountAction constructor.
	 * @param RendererInterface $renderer
	 * @param AuthInterface     $auth
	 */
	public function __construct(RendererInterface $renderer, AuthInterface $auth)
	{
		$this->renderer = $renderer;
		$this->auth     = $auth;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return string
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$user = $this->auth->getUser();
		return $this->renderer->render('@account/account', compact('user'));
	}
}
