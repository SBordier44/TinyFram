<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;
use Twig_Extension;
use Twig_SimpleFunction;

class CsrfExtension extends Twig_Extension
{
	/**
	 * @var CsrfMiddleware
	 */
	private $csrfMiddleware;
	
	/**
	 * CsrfExtension constructor.
	 * @param CsrfMiddleware $csrfMiddleware
	 */
	public function __construct(CsrfMiddleware $csrfMiddleware)
	{
		$this->csrfMiddleware = $csrfMiddleware;
	}
	
	/**
	 * @return array
	 */
	public function getFunctions(): array
	{
		return [
			new Twig_SimpleFunction('csrf_input', [$this, 'csrfInput'], ['is_safe' => ['html']])
		];
	}
	
	/**
	 * @return string
	 */
	public function csrfInput(): string
	{
		return '<input type="hidden" ' . 'name="' . $this->csrfMiddleware->getFormKey() . '" ' . 'value="' . $this->csrfMiddleware->generateToken() . '"/>';
	}
}
