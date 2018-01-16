<?php

namespace App\Basket\Action;

use App\Auth\Entity\User;
use App\Basket\Helper\BasketHelper;
use App\Basket\Helper\PurchaseBasketHelper;
use Framework\Auth\AuthInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class OrderProcessAction
{
    /**
     * @var PurchaseBasketHelper
     */
    private $purchaseBasket;
    /**
     * @var AuthInterface
     */
    private $auth;
    /**
     * @var FlashService
     */
    private $flashService;
    /**
     * @var BasketHelper
     */
    private $basketHelper;
    
    /**
     * PurchaseProcessAction constructor.
     * @param PurchaseBasketHelper $purchaseBasketHelper
     * @param AuthInterface        $auth
     * @param FlashService         $flashService
     * @param BasketHelper         $basketHelper
     */
    public function __construct(
        PurchaseBasketHelper $purchaseBasketHelper,
        AuthInterface $auth,
        FlashService $flashService,
        BasketHelper $basketHelper
    ) {
        $this->purchaseBasket = $purchaseBasketHelper;
        $this->auth           = $auth;
        $this->flashService   = $flashService;
        $this->basketHelper   = $basketHelper;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $params      = $request->getParsedBody();
        $stripeToken = $params['stripeToken'];
        /** @var User $user */
        $user = $this->auth->getUser();
        $this->purchaseBasket->process($this->basketHelper, $user, $stripeToken);
        $this->basketHelper->empty();
        $this->flashService->success('Merci pour votre achat');
        return new RedirectResponse('/');
    }
}
