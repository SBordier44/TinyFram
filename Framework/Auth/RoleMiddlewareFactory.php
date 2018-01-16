<?php

namespace Framework\Auth;

use Framework\Middleware\RoleMiddleware;

class RoleMiddlewareFactory
{
	/**
	 * @var AuthInterface
	 */
	private $auth;
	
	/**
	 * RoleMiddlewareFactory constructor.
	 * @param AuthInterface $auth
	 */
	public function __construct(AuthInterface $auth)
	{
		$this->auth = $auth;
	}
	
	/**
	 * @param string $role
	 * @return RoleMiddleware
	 */
	public function makeForRole(string $role): RoleMiddleware
	{
		return new RoleMiddleware($this->auth, $role);
	}
}
