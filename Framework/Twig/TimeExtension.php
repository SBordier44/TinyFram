<?php

namespace Framework\Twig;

use DateTime;
use Twig_Extension;
use Twig_SimpleFilter;

class TimeExtension extends Twig_Extension
{
	/**
	 * @return array
	 */
	public function getFilters(): array
	{
		return [
			new Twig_SimpleFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
		];
	}
	
	/**
	 * @param DateTime $date
	 * @param string   $format
	 * @return string
	 */
	public function ago(DateTime $date, string $format = 'd/m/Y H:i'): string
	{
		return '<span class="timeago" datetime="' . $date->format(DateTime::ATOM) . '">' . $date->format($format) . '</span>';
	}
}
