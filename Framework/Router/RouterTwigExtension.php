<?php

namespace Framework\Router;

use Twig_SimpleFunction;

class RouterTwigExtension extends \Twig_Extension
{
	/**
	 * @var Router
	 */
	private $router;
	
	/**
	 * RouterTwigExtension constructor.
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}
	
	/**
	 * @return array
	 */
	public function getFunctions(): array
	{
		return [
			new Twig_SimpleFunction('path', [$this, 'pathFor']),
			new Twig_SimpleFunction('is_subpath', [$this, 'isSubPath'])
		];
	}
	
	/**
	 * @param string $path
	 * @param array  $params
	 * @return string
	 * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
	 */
	public function pathFor(string $path, array $params = []): string
	{
		return $this->router->generateUri($path, $params);
	}
	
	/**
	 * @param string $path
	 * @return bool
	 * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
	 */
	public function isSubpath(string $path): bool
	{
		$uri         = $_SERVER['REQUEST_URI'] ?? '/';
		$expectedUri = $this->router->generateUri($path);
		return strpos($uri, $expectedUri) !== false;
	}
}
