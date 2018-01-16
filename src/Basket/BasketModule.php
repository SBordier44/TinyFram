<?php

namespace App\Basket;

use App\Basket\Action\BasketAction;
use App\Basket\Action\OrderInvoiceAction;
use App\Basket\Action\OrderListingAction;
use App\Basket\Action\OrderProcessAction;
use App\Basket\Action\OrderSummaryAction;
use App\Basket\Helper\FusionBasketHelper;
use Framework\Event\EventManager;
use Framework\Middleware\LoggedInMiddleware;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;

class BasketModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const MIGRATIONS  = __DIR__ . '/db/migrations';
    public const NAME        = 'basket';
    
    /**
     * BasketModule constructor.
     * @param Router             $router
     * @param RendererInterface  $renderer
     * @param EventManager       $eventManager
     * @param FusionBasketHelper $fusionBasketHelper
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     */
    public function __construct(
        Router $router,
        RendererInterface $renderer,
        EventManager $eventManager,
        FusionBasketHelper $fusionBasketHelper
    ) {
        $renderer->addPath('basket', __DIR__ . '/views');
        $router->post('/cart/add/{id:\d+}', BasketAction::class, 'basket.add');
        $router->post('/cart/change/{id:\d+}', BasketAction::class, 'basket.change');
        $router->get('/cart', BasketAction::class, 'basket');
        $router->post('/cart/summary', [LoggedInMiddleware::class, OrderSummaryAction::class], 'basket.order.summary');
        $router->post('/cart/process', [LoggedInMiddleware::class, OrderProcessAction::class], 'basket.order.process');
        $router->get('/my-orders', [LoggedInMiddleware::class, OrderListingAction::class], 'basket.orders');
        $router->get(
            '/my-orders/{id:\d+}',
            [LoggedInMiddleware::class, OrderInvoiceAction::class],
            'basket.order.invoice'
        );
        $eventManager->attach('auth.login', $fusionBasketHelper);
    }
}
