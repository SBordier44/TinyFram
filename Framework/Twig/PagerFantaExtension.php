<?php

namespace Framework\Twig;

use Framework\Router\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use Twig_Extension;
use Twig_SimpleFunction;

class PagerFantaExtension extends Twig_Extension
{
	/**
	 * @var Router
	 */
	private $router;
	
	/**
	 * PagerFantaExtension constructor.
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
			new Twig_SimpleFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
		];
	}
	
	/**
	 * @param Pagerfanta $paginatedResults
	 * @param string     $route
	 * @param array      $routerParams
	 * @param array      $queryArgs
	 * @return string
	 */
	public function paginate(
		Pagerfanta $paginatedResults,
		string $route,
		array $routerParams = [],
		array $queryArgs = []
	): string {
		
		$view = new TwitterBootstrap4View();
		return $view->render($paginatedResults, function (int $page) use ($route, $routerParams, $queryArgs) {
			if ($page > 1) {
				$queryArgs['p'] = $page;
			}
			return $this->router->generateUri($route, $routerParams, $queryArgs);
		}, [
			'prev_message' => '<span class="oi oi-arrow-left"></span> Précédent',
			'next_message' => 'Suivant <span class="oi oi-arrow-right"></span>',
			'proximity'    => 3
		]);
	}
}
