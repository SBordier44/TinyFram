<?php

namespace Framework\Api;

use Stripe\{
	AttachedObject, Card, Charge, Customer, Stripe as StripeApi, Token
};

class Stripe
{
	/**
	 * Stripe constructor.
	 * @param string $token
	 */
	public function __construct(string $token)
	{
		StripeApi::setApiKey($token);
	}
	
	/**
	 * @param string $token
	 * @return AttachedObject
	 */
	public function getCardFromToken(string $token): AttachedObject
	{
		return Token::retrieve($token)->card;
	}
	
	/**
	 * @param string $customerId
	 * @return Customer
	 */
	public function getCustomer(string $customerId): Customer
	{
		return Customer::retrieve($customerId);
	}
	
	/**
	 * @param array $params
	 * @return Customer
	 */
	public function createCustomer(array $params): Customer
	{
		return Customer::create($params);
	}
	
	/**
	 * @param Customer $customer
	 * @param string   $token
	 * @return Card
	 */
	public function createCardForCustomer(Customer $customer, string $token): Card
	{
		return $customer->sources->create(['source' => $token]);
	}
	
	/**
	 * @param array $params
	 * @return Charge
	 */
	public function createCharge(array $params): Charge
	{
		return Charge::create($params);
	}
}
