<?php

namespace App\Basket\Helper;

use App\Auth\Entity\User;
use App\Basket\Table\BasketTable;
use App\Basket\Table\OrderTable;
use App\Shop\Table\StripeUserTable;
use Framework\Api\Stripe;
use Staaky\VATRates\VATRates;
use Stripe\AttachedObject;
use Stripe\Card;
use Stripe\Customer;

class PurchaseBasketHelper
{
	/**
	 * @var OrderTable
	 */
	private $orderTable;
	/**
	 * @var Stripe
	 */
	private $stripe;
	/**
	 * @var StripeUserTable
	 */
	private $stripeUserTable;
	/**
	 * @var BasketTable
	 */
	private $basketTable;
	
	/**
	 * PurchaseProduct constructor.
	 * @param OrderTable      $orderTable
	 * @param BasketTable     $basketTable
	 * @param Stripe          $stripe
	 * @param StripeUserTable $stripeUserTable
	 * @internal param PurchaseTable $purchaseTable
	 */
	public function __construct(
		OrderTable $orderTable,
		BasketTable $basketTable,
		Stripe $stripe,
		StripeUserTable $stripeUserTable
	) {
		$this->orderTable      = $orderTable;
		$this->stripe          = $stripe;
		$this->stripeUserTable = $stripeUserTable;
		$this->basketTable     = $basketTable;
	}
	
	/**
	 * @param BasketHelper $basketHelper
	 * @param User         $user
	 * @param string       $token
	 * @internal param Product $product
	 */
	public function process(BasketHelper $basketHelper, User $user, string $token): void
	{
		$this->basketTable->hydrateBasket($basketHelper);
		$card     = $this->stripe->getCardFromToken($token);
		$vatRate  = (new VATRates())->getStandardRate($card->country) ? : 0;
		$price    = floor($basketHelper->getPrice() * ((100 + $vatRate) / 100));
		$customer = $this->findCustomerForUser($user, $token);
		$card     = $this->getMatchingCard($customer, $card);
		if ($card === null) {
			$card = $this->stripe->createCardForCustomer($customer, $token);
		}
		$charge = $this->stripe->createCharge([
			'amount'      => $price * 100,
			'currency'    => 'eur',
			'source'      => $card->id,
			'customer'    => $customer->id,
			'description' => 'Achat sur monsite.com'
		]);
		$this->orderTable->createFromBasket($basketHelper, [
			'user_id'   => $user->getId(),
			'vat'       => $vatRate,
			'country'   => $card->country,
			'stripe_id' => $charge->id
		]);
	}
	
	/**
	 * @param User   $user
	 * @param string $token
	 * @return Customer
	 */
	private function findCustomerForUser(User $user, string $token): Customer
	{
		$customerId = $this->stripeUserTable->findCustomerForUser($user);
		if ($customerId) {
			$customer = $this->stripe->getCustomer($customerId);
		} else {
			$customer = $this->stripe->createCustomer([
				'email'  => $user->getEmail(),
				'source' => $token
			]);
			$this->stripeUserTable->insert([
				'user_id'     => $user->getId(),
				'customer_id' => $customer->id,
				'created_at'  => date('Y-m-d H:i:s')
			]);
		}
		return $customer;
	}
	
	/**
	 * @param Customer       $customer
	 * @param AttachedObject $card
	 * @return null|Card
	 */
	private function getMatchingCard(Customer $customer, AttachedObject $card): ?Card
	{
		foreach ((array)$customer->sources->data as $c) {
			if ($c->fingerprint === $card->fingerprint) {
				return $c;
			}
		}
		return null;
	}
}
