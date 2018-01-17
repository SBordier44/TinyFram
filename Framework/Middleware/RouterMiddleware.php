<?php

namespace Framework\Middleware;

use Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware
{
    /**
     * @var Router
     */
    private $router;
    
    /**
     * RouterMiddleware constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param callable               $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $this->router->match($request);
        if (null === $route) {
            return $next($request);
        }
        $params  = $route->getParams();
        $request = array_reduce(array_keys($params), function (ServerRequestInterface $request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(\get_class($route), $route);
        return $next($request);
    }
}
