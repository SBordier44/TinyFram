<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\MethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddlewareTest extends TestCase
{
    /**
     * @var MethodMiddleware
     */
    private $middleware;
    
    public function setUp()
    {
        $this->middleware = new MethodMiddleware();
    }
    
    public function testAddMethod()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        $delegate->expects(static::once())->method('handle')->with(static::callback(function ($request) {
            return $request->getMethod() === 'DELETE';
        }));
        $request = (new ServerRequest('POST', '/demo'))->withParsedBody(['_method' => 'DELETE']);
        $this->middleware->process($request, $delegate);
    }
}
