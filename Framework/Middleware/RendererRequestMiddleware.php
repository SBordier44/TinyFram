<?php

namespace Framework\Middleware;

use Framework\Renderer\RendererInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function sprintf;

class RendererRequestMiddleware implements MiddlewareInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    
    /**
     * Constructor of RendererRequestMiddleware
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $domain = sprintf(
            '%s://%s%s',
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $request->getUri()->getPort() ? ':' . $request->getUri()->getPort() : ''
        );
        $this->renderer->addGlobal('domain', $domain);
        return $delegate->handle($request);
    }
}
