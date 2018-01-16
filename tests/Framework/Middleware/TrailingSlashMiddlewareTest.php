<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\TrailingSlashMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class TrailingSlashMiddlewareTest extends TestCase
{
    
    public function testRedirectIfSlash()
    {
        $request    = new ServerRequest('GET', '/demo/');
        $middleware = new TrailingSlashMiddleware();
        $response   = $middleware($request, function () {
        });
        static::assertEquals(['/demo'], $response->getHeader('Location'));
        static::assertEquals(301, $response->getStatusCode());
    }
    
    public function testCallNextIfNoSlash()
    {
        $request    = new ServerRequest('GET', '/demo');
        $response   = new Response();
        $middleware = new TrailingSlashMiddleware();
        $callback   = function () use ($response) {
            return $response;
        };
        static::assertEquals($response, $middleware($request, $callback));
    }
}
