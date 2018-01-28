<?php

namespace Tests\Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    
    /**
     * @var CsrfMiddleware
     */
    private $middleware;
    private $session;
    
    public function setUp()
    {
        $this->session    = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }
    
    public function testLetGetRequestPass()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        
        $delegate->expects($this->once())->method('handle')->willReturn(new Response());
        
        $request = new ServerRequest('GET', '/demo');
        $this->middleware->process($request, $delegate);
    }
    
    public function testBlockPostRequestWithoutCsrf()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        
        $delegate->expects(static::never())->method('handle');
        
        $request = new ServerRequest('POST', '/demo');
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }
    
    public function testBlockPostRequestWithInvalidCsrf()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        
        $delegate->expects(static::never())->method('handle');
        $this->middleware->generateToken();
        $request = new ServerRequest('POST', '/demo');
        $request = $request->withParsedBody(['_csrf' => 'azeaz']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $delegate);
    }
    
    public function testLetPostWithTokenPass()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        
        $delegate->expects(static::once())->method('handle')->willReturn(new Response());
        
        $request = new ServerRequest('POST', '/demo');
        $token   = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $delegate);
    }
    
    public function testLetPostWithTokenPassOnce()
    {
        $delegate = $this->getMockBuilder(RequestHandlerInterface::class)->setMethods(['handle'])->getMock();
        
        $delegate->expects(static::once())->method('handle')->willReturn(new Response());
        
        $request = new ServerRequest('POST', '/demo');
        $token   = $this->middleware->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->middleware->process($request, $delegate);
        $this->expectException(\Exception::class);
        $this->middleware->process($request, $delegate);
    }
    
    public function testLimitTheTokenNumber()
    {
        for ($i = 0; $i < 100; ++$i) {
            $token = $this->middleware->generateToken();
        }
        static::assertCount(50, $this->session['csrf']);
        static::assertEquals($token, $this->session['csrf'][49]);
    }
}
