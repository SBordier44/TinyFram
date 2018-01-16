<?php

namespace App\Admin\Twig;

use App\Admin\AdminWidgetInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class AdminTwigExtension extends Twig_Extension
{
	/**
	 * @var array
	 */
	private $widgets;
	
	/**
	 * AdminTwigExtension constructor.
	 * @param array $widgets
	 */
	public function __construct(array $widgets)
	{
		$this->widgets = $widgets;
	}
	
	/**
	 * @return array
	 */
	public function getFunctions(): array
	{
		return [
			new Twig_SimpleFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
		];
	}
	
	/**
	 * @return string
	 */
	public function renderMenu(): string
	{
		return array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
			return $html . $widget->renderMenu();
		}, '');
	}
}
