<?php

namespace Tests\Auth;

use App\Auth\ForbiddenMiddleware;
use Framework\Auth\AuthInterface;
use Framework\Auth\UserInterface;
use Framework\Exception\ForbiddenException;
use Framework\Session\ArraySession;
use Framework\Session\SessionInterface;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TypeError;

class ForbiddenMiddlewareTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $auth;
    /**
     * @var SessionInterface
     */
    private $session;
    
    public function setUp()
    {
        $this->session = new ArraySession();
        $this->auth    = $this->prophesize(AuthInterface::class);
    }
    
    public function testCatchForbiddenException()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects(static::once())->method('handle')->willThrowException(new ForbiddenException());
        $response = $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        static::assertEquals(301, $response->getStatusCode());
        static::assertEquals(['/login'], $response->getHeader('Location'));
        static::assertEquals('/test', $this->session->get('auth.redirect'));
    }
    
    private function makeDelegate()
    {
        return $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
    }
    
    private function makeMiddleware()
    {
        return new ForbiddenMiddleware('/login', '/account', $this->session, $this->auth->reveal());
    }
    
    private function makeRequest($path = '/')
    {
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn($path);
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getUri')->willReturn($uri);
        return $request;
    }
    
    public function testCatchTypeErrorException()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects(static::once())->method('handle')->willReturnCallback(function (UserInterface $user) {
            return true;
        });
        $response = $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        static::assertEquals(301, $response->getStatusCode());
        static::assertEquals(['/login'], $response->getHeader('Location'));
        static::assertEquals('/test', $this->session->get('auth.redirect'));
    }
    
    
    public function testBubbleError()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects(static::once())->method('handle')->willReturnCallback(function () {
            throw new TypeError('test', 200);
        });
        try {
            $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        } catch (TypeError $e) {
            static::assertEquals('test', $e->getMessage());
            static::assertEquals(200, $e->getCode());
        }
    }
    
    public function testProcessValidRequest()
    {
        $delegate = $this->makeDelegate();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $delegate->expects(static::once())->method('handle')->willReturn($response);
        static::assertSame($response, $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate));
    }
}
