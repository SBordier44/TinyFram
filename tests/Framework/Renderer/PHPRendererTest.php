<?php

namespace Tests\Framework\Renderer;

use Framework\Renderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{
	/**
	 * @var Renderer\PHPRenderer
	 */
	private $renderer;
	
	public function setUp()
	{
		$this->renderer = new Renderer\PHPRenderer(__DIR__ . '/views');
	}
	
	public function testRenderTheRightPath()
	{
		$this->renderer->addPath('blog', __DIR__ . '/views');
		$content = $this->renderer->render('@blog/demo');
		static::assertEquals('Salut les gens', $content);
	}
	
	public function testRenderTheDefaultPath()
	{
		$content = $this->renderer->render('demo');
		static::assertEquals('Salut les gens', $content);
	}
	
	public function testRenderWithParams()
	{
		$content = $this->renderer->render('demoparams', ['nom' => 'Marc']);
		static::assertEquals('Salut Marc', $content);
	}
	
	public function testGlobalParameters()
	{
		$this->renderer->addGlobal('nom', 'Marc');
		$content = $this->renderer->render('demoparams');
		static::assertEquals('Salut Marc', $content);
	}
}
