<?php

namespace Framework\Entity;

use DateTime;

trait TimestampTrait
{
	/**
	 * @var DateTime|null
	 */
	private $updatedAt;
	/**
	 * @var DateTime|null
	 */
	private $createdAt;
	
	/**
	 * @return DateTime|null
	 */
	public function getUpdatedAt(): ?\DateTime
	{
		return $this->updatedAt;
	}
	
	/**
	 * @param DateTime|null|string $datetime
	 * @return self
	 */
	public function setUpdatedAt($datetime): self
	{
		$this->updatedAt = \is_string($datetime) ? new \DateTime($datetime) : $datetime;
		return $this;
	}
	
	/**
	 * @return DateTime|null
	 */
	public function getCreatedAt(): ?\DateTime
	{
		return $this->createdAt;
	}
	
	/**
	 * @param DateTime|null|string $datetime
	 * @return self
	 */
	public function setCreatedAt($datetime): self
	{
		$this->createdAt = \is_string($datetime) ? new \DateTime($datetime) : $datetime;
		return $this;
	}
}
