<?php

namespace Framework\Renderer;

class TwigRenderer implements RendererInterface
{
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	/**
	 * TwigRenderer constructor.
	 * @param \Twig_Environment $twig
	 */
	public function __construct(\Twig_Environment $twig)
	{
		$this->twig = $twig;
	}
	
	/**
	 * @param string      $namespace
	 * @param null|string $path
	 */
	public function addPath(string $namespace, ?string $path = null): void
	{
		$this->twig->getLoader()->addPath($path, $namespace);
	}
	
	/**
	 * @param string $view
	 * @param array  $params
	 * @return string
	 * @throws \Twig_Error_Syntax
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Loader
	 */
	public function render(string $view, array $params = []): string
	{
		return $this->twig->render($view . '.twig', $params);
	}
	
	/**
	 * @param string $key
	 * @param mixed  $value
	 * @throws \LogicException
	 */
	public function addGlobal(string $key, $value): void
	{
		$this->twig->addGlobal($key, $value);
	}
	
	/**
	 * @return \Twig_Environment
	 */
	public function getTwig(): \Twig_Environment
	{
		return $this->twig;
	}
}
