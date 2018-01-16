<?php

namespace Tests\Framework\Middleware;

use Framework\Auth\{
	AuthInterface, UserInterface
};
use Framework\Exception\ForbiddenException;
use Framework\Middleware\RoleMiddleware;
use GuzzleHttp\Psr7\{
	Response, ServerRequest
};
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class RoleMiddlewareTest extends TestCase
{
	/**
	 * @var ObjectProphecy
	 */
	private $auth;
	/**
	 * @var RoleMiddleware
	 */
	private $middleware;
	
	public function testWithBadRole()
	{
		$user = $this->prophesize(UserInterface::class);
		$user->getRoles()->willReturn(['user']);
		$this->auth->getUser()->willReturn($user->reveal());
		$this->expectException(ForbiddenException::class);
		$this->middleware->process(new ServerRequest('GET', '/demo'), $this->makeDelegate()->reveal());
	}
	
	private function makeDelegate(): ObjectProphecy
	{
		$delegate = $this->prophesize(RequestHandlerInterface::class);
		$delegate->handle(Argument::any())->willReturn(new Response());
		return $delegate;
	}
	
	public function testWithUnauthenticatedUser()
	{
		$this->auth->getUser()->willReturn(null);
		$this->expectException(ForbiddenException::class);
		$this->middleware->process(new ServerRequest('GET', '/demo'), $this->makeDelegate()->reveal());
	}
	
	public function testWithGoodRole()
	{
		$user = $this->prophesize(UserInterface::class);
		$user->getRoles()->willReturn(['admin']);
		$this->auth->getUser()->willReturn($user->reveal());
		$delegate = $this->makeDelegate();
		$delegate->handle(Argument::any())->shouldBeCalled()->willReturn(new Response());
		$this->middleware->process(new ServerRequest('GET', '/demo'), $delegate->reveal());
	}
	
	public function setUp()
	{
		$this->auth       = $this->prophesize(AuthInterface::class);
		$this->middleware = new RoleMiddleware($this->auth->reveal(), 'admin');
	}
}
