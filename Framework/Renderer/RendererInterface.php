<?php

namespace Framework\Renderer;

interface RendererInterface
{
	/**
	 * @param string      $namespace
	 * @param null|string $path
	 */
	public function addPath(string $namespace, ?string $path = null): void;
	
	/**
	 * @param string $view
	 * @param array  $params
	 * @return string
	 */
	public function render(string $view, array $params = []): string;
	
	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function addGlobal(string $key, $value): void;
}
