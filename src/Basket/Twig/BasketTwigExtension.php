<?php

namespace App\Basket\Twig;

use App\Basket\Helper\BasketHelper;
use Twig_Extension;
use Twig_SimpleFunction;

class BasketTwigExtension extends Twig_Extension
{
    /**
     * @var BasketHelper
     */
    private $basket;
    
    /**
     * BasketTwigExtension constructor.
     * @param BasketHelper $basket
     */
    public function __construct(BasketHelper $basket)
    {
        $this->basket = $basket;
    }
    
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('basket_count', [$this->basket, 'count'])
        ];
    }
}
