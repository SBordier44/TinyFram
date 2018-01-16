<?php

namespace Framework\Middleware;

use Framework\Auth\AuthInterface;
use Framework\Exception\ForbiddenException;
use Interop\Http\Server\{
	MiddlewareInterface, RequestHandlerInterface
};
use Psr\Http\Message\{
	ResponseInterface, ServerRequestInterface
};

class LoggedInMiddleware implements MiddlewareInterface
{
	
	/**
	 * @var AuthInterface
	 */
	private $auth;
	
	/**
	 * LoggedInMiddleware constructor.
	 * @param AuthInterface $auth
	 */
	public function __construct(AuthInterface $auth)
	{
		$this->auth = $auth;
	}
	
	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $delegate
	 * @return ResponseInterface
	 * @throws ForbiddenException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
	{
		$user = $this->auth->getUser();
		if (null === $user) {
			throw new ForbiddenException('');
		}
		return $delegate->handle($request->withAttribute('user', $user));
	}
}
