<?php

namespace App\Shop\Action;

use App\Shop\Table\ProductTable;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class ProductListingAction
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
	 * ProductListingAction constructor.
	 * @param RendererInterface $renderer
	 * @param ProductTable      $productTable
	 */
	public function __construct(RendererInterface $renderer, ProductTable $productTable)
	{
		$this->renderer     = $renderer;
		$this->productTable = $productTable;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return string
	 * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
	 * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
	 * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
	 * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
	 * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$params   = $request->getQueryParams();
		$page     = $params['p'] ?? 1;
		$products = $this->productTable->findPublic()->paginate(12, $page);
		return $this->renderer->render('@shop/index', compact('products', 'page'));
	}
}
