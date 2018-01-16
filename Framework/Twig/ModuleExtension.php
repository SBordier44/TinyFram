<?php

namespace Framework\Twig;

use Framework\App;
use Twig_Extension;
use Twig_SimpleFunction;

class ModuleExtension extends Twig_Extension
{
	/**
	 * @var App
	 */
	private $app;
	
	/**
	 * ModuleExtension constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}
	
	/**
	 * @return array
	 */
	public function getFunctions(): array
	{
		return [
			new Twig_SimpleFunction('module_enabled', [$this, 'moduleEnabled'])
		];
	}
	
	/**
	 * @param string $moduleName
	 * @return bool
	 */
	public function moduleEnabled(string $moduleName): bool
	{
		foreach ($this->app->getModules() as $module) {
			if ($module::NAME === $moduleName) {
				return true;
			}
		}
		return false;
	}
}
