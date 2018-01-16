<?php

namespace App\Account\Entity;

use App\Auth\Entity\User as UserBase;

class User extends UserBase
{
	/**
	 * @var string|null
	 */
	private $firstname;
	/**
	 * @var string|null
	 */
	private $lastname;
	/**
	 * @var string|null
	 */
	private $role;
	
	/**
	 * @return string|null
	 */
	public function getFirstname(): ?string
	{
		return $this->firstname;
	}
	
	/**
	 * @param string|null $firstname
	 * @return User
	 */
	public function setFirstname(?string $firstname): self
	{
		$this->firstname = $firstname;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getLastname(): ?string
	{
		return $this->lastname;
	}
	
	/**
	 * @param string|null $lastname
	 * @return User
	 */
	public function setLastname(?string $lastname): self
	{
		$this->lastname = $lastname;
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getRoles(): array
	{
		return [$this->role];
	}
	
	/**
	 * @return string|null
	 */
	public function getRole(): ?string
	{
		return $this->role;
	}
	
	/**
	 * @param string|null $role
	 * @return User
	 */
	public function setRole(?string $role): self
	{
		$this->role = $role;
		return $this;
	}
}
