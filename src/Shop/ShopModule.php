<?php

namespace App\Shop;

use App\Shop\Action\AdminProductAction;
use App\Shop\Action\ProductListingAction;
use App\Shop\Action\ProductShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Psr\Container\ContainerInterface;

class ShopModule extends Module
{
    public const MIGRATIONS  = __DIR__ . '/db/migrations';
    public const SEEDS       = __DIR__ . '/db/seeds';
    public const DEFINITIONS = __DIR__ . '/config.php';
    
    /**
     * ShopModule constructor.
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('shop', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get('/shop', ProductListingAction::class, 'shop');
        $router->get('/shop/{slug}', ProductShowAction::class, 'shop.show');
        $router->crud($container->get('admin.prefix') . '/products', AdminProductAction::class, 'shop.admin.products');
    }
}
