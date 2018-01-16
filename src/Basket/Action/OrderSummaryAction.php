<?php

namespace App\Basket\Action;

use App\Basket\Helper\BasketHelper;
use App\Basket\Table\BasketTable;
use Framework\Api\Stripe;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Staaky\VATRates\VATRates;

class OrderSummaryAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Stripe
     */
    private $stripe;
    /**
     * @var BasketTable
     */
    private $basketTable;
    /**
     * @var BasketHelper
     */
    private $basketHelper;
    
    /**
     * PurchaseRecapAction constructor.
     * @param RendererInterface $renderer
     * @param BasketTable       $basketTable
     * @param Stripe            $stripe
     * @param BasketHelper      $basketHelper
     * @internal param ProductTable $productTable
     */
    public function __construct(
        RendererInterface $renderer,
        BasketTable $basketTable,
        Stripe $stripe,
        BasketHelper $basketHelper
    ) {
        $this->renderer     = $renderer;
        $this->stripe       = $stripe;
        $this->basketTable  = $basketTable;
        $this->basketHelper = $basketHelper;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params      = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        $card        = $this->stripe->getCardFromToken($stripeToken);
        $vat         = (new VATRates())->getStandardRate($card->country);
        $basket      = $this->basketHelper;
        $this->basketTable->hydrateBasket($basket);
        $price = floor($basket->getPrice() * (($vat + 100) / 100));
        return $this->renderer->render('@basket/summary', compact('basket', 'price', 'stripeToken', 'vat', 'card'));
    }
}
