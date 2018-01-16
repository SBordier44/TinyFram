<?php

namespace App\Basket\Entity;

use DateTime;

class Order
{
	/**
	 * @var int|null
	 */
	private $id;
	/**
	 * @var int|null
	 */
	private $userId;
	/**
	 * @var float|null
	 */
	private $vat;
	/**
	 * @var float|null
	 */
	private $price;
	/**
	 * @var string|null
	 */
	private $country;
	/**
	 * @var DateTime|null
	 */
	private $createdAt;
	/**
	 * @var string|null
	 */
	private $stripeId;
	/**
	 * @var OrderRow[]|null
	 */
	private $rows = [];
	
	/**
	 * @return OrderRow[]|null
	 */
	public function getRows(): ?array
	{
		return $this->rows;
	}
	
	/**
	 * @param OrderRow[] $rows
	 * @return Order|null
	 */
	public function setRows(?array $rows): Order
	{
		$this->rows = $rows;
		return $this;
	}
	
	/**
	 * @param OrderRow $row
	 * @return Order
	 */
	public function addRow(OrderRow $row): Order
	{
		$this->rows[] = $row;
		return $this;
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int
	{
		return $this->id;
	}
	
	/**
	 * @param int|null $id
	 * @return Order
	 */
	public function setId(?int $id): Order
	{
		$this->id = $id;
		return $this;
	}
	
	/**
	 * @return int|null
	 */
	public function getUserId(): ?int
	{
		return $this->userId;
	}
	
	/**
	 * @param int|null $userId
	 * @return Order
	 */
	public function setUserId(?int $userId): Order
	{
		$this->userId = $userId;
		return $this;
	}
	
	/**
	 * @return float|null
	 */
	public function getVat(): ?float
	{
		return $this->vat;
	}
	
	/**
	 * @param float|null $vat
	 * @return Order
	 */
	public function setVat(?float $vat): Order
	{
		$this->vat = $vat;
		return $this;
	}
	
	/**
	 * @return float|null
	 */
	public function getPrice(): ?float
	{
		return $this->price;
	}
	
	/**
	 * @param float|null $price
	 * @return Order
	 */
	public function setPrice(?float $price): Order
	{
		$this->price = $price;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getCountry(): ?string
	{
		return $this->country;
	}
	
	/**
	 * @param string|null $country
	 * @return Order
	 */
	public function setCountry(?string $country): Order
	{
		$this->country = $country;
		return $this;
	}
	
	/**
	 * @return DateTime|null
	 */
	public function getCreatedAt(): ?DateTime
	{
		return $this->createdAt;
	}
	
	/**
	 * @param mixed $createdAt
	 * @return Order
	 */
	public function setCreatedAt($createdAt): Order
	{
		$this->createdAt = \is_string($createdAt) ? new DateTime($createdAt) : $createdAt;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getStripeId(): ?string
	{
		return $this->stripeId;
	}
	
	/**
	 * @param string|null $stripeId
	 * @return Order
	 */
	public function setStripeId(?string $stripeId): Order
	{
		$this->stripeId = $stripeId;
		return $this;
	}
}
