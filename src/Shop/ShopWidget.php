<?php

namespace App\Shop;

use App\Admin\AdminWidgetInterface;
use App\Shop\Table\ProductTable;
use Framework\Renderer\RendererInterface;

class ShopWidget implements AdminWidgetInterface
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
     * ShopWidget constructor.
     * @param RendererInterface $renderer
     * @param ProductTable      $productTable
     */
    public function __construct(RendererInterface $renderer, ProductTable $productTable)
    {
        $this->renderer     = $renderer;
        $this->productTable = $productTable;
    }
    
    /**
     * @return string
     */
    public function render(): string
    {
        $count = $this->productTable->count();
        return $this->renderer->render('@shop/admin/widget', compact('count'));
    }
    
    /**
     * @return string
     */
    public function renderMenu(): string
    {
        return $this->renderer->render('@shop/admin/menu');
    }
}
