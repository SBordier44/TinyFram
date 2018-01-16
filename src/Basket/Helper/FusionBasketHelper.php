<?php

namespace App\Basket\Helper;

use App\Auth\Event\LoginEvent;
use App\Basket\Table\BasketTable;

class FusionBasketHelper
{
    /**
     * @var SessionBasketHelper
     */
    private $basket;
    /**
     * @var BasketTable
     */
    private $basketTable;
    
    /**
     * FusionBasketHelper constructor.
     * @param SessionBasketHelper $basket
     * @param BasketTable         $basketTable
     */
    public function __construct(SessionBasketHelper $basket, BasketTable $basketTable)
    {
        $this->basket      = $basket;
        $this->basketTable = $basketTable;
    }
    
    /**
     * @param LoginEvent $event
     */
    public function __invoke(LoginEvent $event)
    {
        $user = $event->getTarget();
        (new DatabaseBasketHelper($user->getId(), $this->basketTable))->merge($this->basket);
        $this->basket->empty();
    }
}
