<?php

namespace App\Shop\Action;

use App\Shop\Table\ProductTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class ProductShowAction
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var ProductTable
	 */
	private $productTable;
	
	/**
	 * ProductShowAction constructor.
	 * @param RendererInterface $renderer
	 * @param ProductTable      $productTable
	 */
	public function __construct(
		RendererInterface $renderer,
		ProductTable $productTable
	) {
		$this->renderer     = $renderer;
		$this->productTable = $productTable;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return string
	 * @throws \Framework\Database\NoRecordException
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$product = $this->productTable->findBy('slug', $request->getAttribute('slug'));
		return $this->renderer->render('@shop/show', compact('product'));
	}
}
