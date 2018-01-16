<?php

namespace Tests\Framework\Twig;

use Framework\Twig\TimeExtension;
use PHPUnit\Framework\TestCase;

class TimeExtensionTest extends TestCase
{
	
	/**
	 * @var TimeExtension
	 */
	private $timeExtension;
	
	public function setUp()
	{
		$this->timeExtension = new TimeExtension();
	}
	
	public function testDateFormat()
	{
		$date   = new \DateTime();
		$format = 'd/m/Y H:i';
		$result = '<span class="timeago" datetime="' . $date->format(\DateTime::ATOM) . '">' . $date->format($format) . '</span>';
		static::assertEquals($result, $this->timeExtension->ago($date));
	}
}
