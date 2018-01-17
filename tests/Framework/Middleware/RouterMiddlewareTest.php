<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\RouterMiddleware;
use Framework\Router\Route;
use Framework\Router\Router;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterMiddlewareTest extends TestCase
{
    public function testPassParameters()
    {
        $route      = new Route('demo', 'trim', ['id' => 2]);
        $middleware = $this->makeMiddleware($route);
        $demo       = function ($request) use ($route) {
            static::assertEquals(2, $request->getAttribute('id'));
            static::assertEquals($route, $request->getAttribute(\get_class($route)));
            return new Response();
        };
        $middleware(new ServerRequest('GET', '/demo'), $demo);
    }
    
    public function makeMiddleware($route)
    {
        $router = $this->getMockBuilder(Router::class)->getMock();
        $router->method('match')->willReturn($route);
        return new RouterMiddleware($router);
    }
    
    public function testCallNext()
    {
        $route      = new Route('demo', 'trim', ['id' => 2]);
        $middleware = $this->makeMiddleware(null);
        $response   = new Response();
        $demo       = function ($request) use ($response) {
            return $response;
        };
        static::assertEquals($response, $middleware(new ServerRequest('GET', '/demo'), $demo));
    }
}
