<?php

namespace App\Blog;

use App\Admin\AdminWidgetInterface;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;

class BlogWidget implements AdminWidgetInterface
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var PostTable
	 */
	private $postTable;
	
	/**
	 * BlogWidget constructor.
	 * @param RendererInterface $renderer
	 * @param PostTable         $postTable
	 */
	public function __construct(RendererInterface $renderer, PostTable $postTable)
	{
		$this->renderer  = $renderer;
		$this->postTable = $postTable;
	}
	
	/**
	 * @return string
	 */
	public function render(): string
	{
		$count = $this->postTable->count();
		return $this->renderer->render('@blog/admin/widget', compact('count'));
	}
	
	/**
	 * @return string
	 */
	public function renderMenu(): string
	{
		return $this->renderer->render('@blog/admin/menu');
	}
}
