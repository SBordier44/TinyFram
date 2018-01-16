<?php

namespace App\Basket\Helper;

use App\Basket\Entity\BasketRow;
use App\Shop\Entity\Product;
use Framework\Session\SessionInterface;
use function array_map;

class SessionBasketHelper extends BasketHelper
{
    /**
     * @var SessionInterface
     */
    private $session;
    
    /**
     * SessionBasket constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $rows          = $this->session->get('basket', []);
        $this->setRows(array_map(function ($row) {
            $r = new BasketRow();
            $r->setProductId($row['id']);
            $product = new Product();
            $product->setId($row['id']);
            $r->setProduct($product);
            $r->setQuantity($row['quantity']);
            return $r;
        }, $rows));
    }
    
    /**
     * @param Product  $product
     * @param int|null $quantity
     */
    public function addProduct(Product $product, ?int $quantity = null): void
    {
        parent::addProduct($product, $quantity);
        $this->persist();
    }
    
    /**
     * Persist serialized basket in Session
     */
    private function persist(): void
    {
        $this->session->set('basket', $this->serialize());
    }
    
    /**
     * @return array
     */
    private function serialize(): array
    {
        return array_map(function (BasketRow $row) {
            return [
                'id'       => $row->getProductId(),
                'quantity' => $row->getQuantity()
            ];
        }, $this->getRows());
    }
    
    /**
     * @param Product $product
     */
    public function removeProduct(Product $product): void
    {
        parent::removeProduct($product);
        $this->persist();
    }
    
    /**
     * @return void
     */
    public function empty(): void
    {
        parent::empty();
        $this->persist();
    }
}
