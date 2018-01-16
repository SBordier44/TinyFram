<?php

namespace App\Auth\Action;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	
	/**
	 * LoginAction constructor.
	 * @param RendererInterface $renderer
	 */
	public function __construct(RendererInterface $renderer)
	{
		$this->renderer = $renderer;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return string
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		return $this->renderer->render('@auth/login');
	}
}
