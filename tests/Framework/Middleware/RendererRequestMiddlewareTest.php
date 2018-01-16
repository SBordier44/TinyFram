<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\RendererRequestMiddleware;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;

class RendererRequestMiddlewareTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $delegate;
    /**
     * @var ObjectProphecy
     */
    private $renderer;
    /**
     * @var RendererRequestMiddleware
     */
    private $middleware;
    
    public function testAddGlobalDomain()
    {
        $this->renderer->addGlobal('domain', 'http://mydomain.fr')->shouldBeCalled();
        $this->renderer->addGlobal('domain', 'http://localhost:3000')->shouldBeCalled();
        $this->renderer->addGlobal('domain', 'https://localhost')->shouldBeCalled();
        $this->middleware->process(new ServerRequest('GET', 'http://mydomain.fr/blog/demo'), $this->delegate);
        $this->middleware->process(new ServerRequest('GET', 'http://localhost:3000/blog/demo'), $this->delegate);
        $this->middleware->process(new ServerRequest('GET', 'https://localhost/blog/demo'), $this->delegate);
    }
    
    protected function setUp()
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->delegate = $this->prophesize(RequestHandlerInterface::class);
        $this->delegate->handle(Argument::type(ServerRequestInterface::class))->willReturn(new Response());
        $this->delegate   = $this->delegate->reveal();
        $this->middleware = new RendererRequestMiddleware($this->renderer->reveal());
    }
}
