<?php

namespace App\Basket\Action;

use App\Basket\Helper\BasketHelper;
use App\Basket\Table\BasketTable;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectBackResponse;
use Psr\Http\Message\ServerRequestInterface;

class BasketAction
{
    /**
     * @var BasketHelper
     */
    private $basket;
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var BasketTable
     */
    private $basketTable;
    /**
     * @var string
     */
    private $stripeKey;
    
    /**
     * BasketAction constructor.
     * @param BasketHelper      $basket
     * @param RendererInterface $renderer
     * @param BasketTable       $basketTable
     * @param string            $stripeKey
     */
    public function __construct(
        BasketHelper $basket,
        RendererInterface $renderer,
        BasketTable $basketTable,
        string $stripeKey
    ) {
        $this->basket      = $basket;
        $this->renderer    = $renderer;
        $this->basketTable = $basketTable;
        $this->stripeKey   = $stripeKey;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectBackResponse|string
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $product = $this->basketTable->getProductTable()->find((int)$request->getAttribute('id'));
            $params  = $request->getParsedBody();
            $this->basket->addProduct($product, $params['quantity'] ?? null);
            return new RedirectBackResponse($request);
        }
        return $this->show();
    }
    
    /**
     * @return string
     */
    private function show(): string
    {
        $this->basketTable->hydrateBasket($this->basket);
        return $this->renderer->render('@basket/show', [
            'basket'    => $this->basket,
            'stripeKey' => $this->stripeKey
        ]);
    }
}
