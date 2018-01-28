<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\DispatcherMiddleware;
use Framework\Router\Route;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DispatcherMiddlewareTest extends TestCase
{
    public function testDispatchTheCallback()
    {
        $callback   = function () {
            return 'Hello';
        };
        $route      = new Route('demo', $callback, []);
        $request    = (new ServerRequest('GET', '/demo'))->withAttribute(Route::class, $route);
        $container  = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $dispatcher = new DispatcherMiddleware($container);
        $response   = $dispatcher->process($request, $this->getMockBuilder(RequestHandlerInterface::class)->getMock());
        static::assertEquals('Hello', (string)$response->getBody());
    }
    
    public function testCallNextIfNotRoutes()
    {
        $response  = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $delegate  = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $delegate->expects(static::once())->method('handle')->willReturn($response);
        $request    = new ServerRequest('GET', '/demo');
        $dispatcher = new DispatcherMiddleware($container);
        static::assertEquals($response, $dispatcher->process($request, $delegate));
    }
}
