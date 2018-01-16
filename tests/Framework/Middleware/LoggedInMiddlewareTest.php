<?php

namespace Tests\Framework\Middleware;

use Framework\Auth\{
	AuthInterface, UserInterface
};
use Framework\Exception\ForbiddenException;
use Framework\Middleware\LoggedInMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class LoggedInMiddlewareTest extends TestCase
{
	
	public function testThrowIfNoUser()
	{
		$request = new ServerRequest('GET', '/demo/');
		$this->expectException(ForbiddenException::class);
		$this->makeMiddleware(null)->process($request, $this->makeDelegate(static::never()));
	}
	
	public function makeMiddleware($user)
	{
		$auth = $this->getMockBuilder(AuthInterface::class)->getMock();
		$auth->method('getUser')->willReturn($user);
		return new LoggedInMiddleware($auth);
	}
	
	public function makeDelegate($calls)
	{
		$delegate = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
		$response = $this->getMockBuilder(ResponseInterface::class)->getMock();
		$delegate->expects($calls)->method('handle')->willReturn($response);
		return $delegate;
	}
	
	public function testNextIfUser()
	{
		$user    = $this->getMockBuilder(UserInterface::class)->getMock();
		$request = new ServerRequest('GET', '/demo/');
		$this->makeMiddleware($user)->process($request, $this->makeDelegate(static::once()));
	}
}
