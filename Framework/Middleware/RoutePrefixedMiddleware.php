<?php

namespace Framework\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;

class RoutePrefixedMiddleware implements MiddlewareInterface
{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string|MiddlewareInterface
     */
    private $middleware;
    
    /**
     * RoutePrefixedMiddleware constructor.
     * @param ContainerInterface $container
     * @param string             $prefix
     * @param                    $middleware
     */
    public function __construct(ContainerInterface $container, string $prefix, $middleware)
    {
        $this->container  = $container;
        $this->prefix     = $prefix;
        $this->middleware = $middleware;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->prefix) === 0) {
            if (\is_string($this->middleware)) {
                return $this->container->get($this->middleware)->process($request, $delegate);
            }
            return $this->middleware->process($request, $delegate);
        }
        return $delegate->handle($request);
    }
}
