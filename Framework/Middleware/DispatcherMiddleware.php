<?php

namespace Framework\Middleware;

use Framework\Router\Route;
use Psr\Container\ContainerInterface;
use Interop\Http\Server\{
	MiddlewareInterface, RequestHandlerInterface
};
use Psr\Http\Message\{
	ResponseInterface, ServerRequestInterface
};
use function is_array;

class DispatcherMiddleware implements MiddlewareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * DispatcherMiddleware constructor.
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
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
		$route = $request->getAttribute(Route::class);
		if (null === $route) {
			return $delegate->handle($request);
		}
		$callback = $route->getCallback();
		if (!is_array($callback)) {
			$callback = [$callback];
		}
		return (new CombinedMiddleware($this->container, $callback))->process($request, $delegate);
	}
}
