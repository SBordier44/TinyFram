<?php

namespace Framework\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use function number_format;

class PriceExtension extends Twig_Extension
{
    /**
     * @var string
     */
    private $currency;
    
    /**
     * PriceExtension constructor.
     * @param string $currency
     */
    public function __construct(string $currency = '€')
    {
        $this->currency = $currency;
    }
    
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('price_format', [$this, 'priceFormat'])
        ];
    }
    
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('vat', [$this, 'getVat']),
            new Twig_SimpleFunction('vat_only', [$this, 'getVatOnly'])
        ];
    }
    
    /**
     * @param float       $price
     * @param null|string $currency
     * @return string
     */
    public function priceFormat(float $price, ?string $currency = null): string
    {
        return number_format($price, 2, ',', ' ') . ' ' . ($currency ? : $this->currency);
    }
    
    /**
     * @param float      $price
     * @param float|null $vat
     * @return float
     */
    public function getVat(float $price, ?float $vat): float
    {
        return $price + $this->getVatOnly($price, $vat);
    }
    
    /**
     * @param float      $price
     * @param float|null $vat
     * @return float
     */
    public function getVatOnly(float $price, ?float $vat): float
    {
        if ($vat === null || $vat === 0) {
            return 0;
        }
        return $price * $vat / 100;
    }
}
