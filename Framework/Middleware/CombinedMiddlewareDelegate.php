<?php

namespace Framework\Middleware;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function is_callable;
use function is_string;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddlewareDelegate implements RequestHandlerInterface
{
    
    /**
     * @var array
     */
    private $middlewares;
    /**
     * @var int
     */
    private $index = 0;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var RequestHandlerInterface
     */
    private $delegate;
    
    /**
     * Constructor of CombinedMiddlewareDelegate
     * @param ContainerInterface      $container
     * @param array                   $middlewares
     * @param RequestHandlerInterface $delegate
     */
    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $delegate)
    {
        $this->middlewares = $middlewares;
        $this->container   = $container;
        $this->delegate    = $delegate;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (null === $middleware) {
            return $this->delegate->handle($request);
        }
        if (is_callable($middleware)) {
            $response = $middleware($request, [$this, 'handle']);
            if (is_string($response)) {
                return new Response(200, [], $response);
            }
            return $response;
        }
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }
    
    /**
     * @return mixed|null
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }
}
