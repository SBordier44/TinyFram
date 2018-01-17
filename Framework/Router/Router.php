<?php

namespace Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

class Router
{
    /**
     * @var FastRouteRouter
     */
    private $router;
    
    /**
     * Router constructor.
     * @param null|string $cache
     */
    public function __construct(?string $cache = null)
    {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => null !== $cache,
            FastRouteRouter::CONFIG_CACHE_FILE    => $cache
        ]);
    }
    
    /**
     * @param string $prefixPath
     * @param        $callable
     * @param string $prefixName
     */
    public function crud(string $prefixPath, $callable, string $prefixName): void
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/{id:\d+}", $callable, "$prefixName.edit");
        $this->post("$prefixPath/{id:\d+}", $callable);
        $this->delete("$prefixPath/{id:\d+}", $callable, "$prefixName.delete");
    }
    
    /**
     * @param string          $path
     * @param string|callable $callable
     * @param string          $name
     */
    public function get(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }
    
    /**
     * @param string          $path
     * @param string|callable $callable
     * @param string          $name
     */
    public function post(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }
    
    /**
     * @param string          $path
     * @param string|callable $callable
     * @param string          $name
     */
    public function delete(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['DELETE'], $name));
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }
    
    /**
     * @param string $name
     * @param array  $params
     * @param array  $queryParams
     * @return null|string
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
    
    /**
     * @param string      $path
     * @param             $callable
     * @param null|string $name
     * @return void
     */
    public function any(string $path, $callable, ?string $name = null): void
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['DELETE', 'POST', 'GET', 'PUT'], $name));
    }
}
