<?php

namespace Framework\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CombinedMiddleware implements MiddlewareInterface
{
    
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $middlewares;
    
    /**
     * CombinedMiddleware constructor.
     * @param ContainerInterface $container
     * @param array              $middlewares
     */
    public function __construct(ContainerInterface $container, array $middlewares)
    {
        $this->container   = $container;
        $this->middlewares = $middlewares;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $delegate = new CombinedMiddlewareDelegate($this->container, $this->middlewares, $delegate);
        
        return $delegate->handle($request);
    }
}
