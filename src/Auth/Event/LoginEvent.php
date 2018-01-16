<?php

namespace App\Auth\Event;

use App\Auth\Entity\User;
use Framework\Event\Event;

class LoginEvent extends Event
{
	/**
	 * @var string
	 */
	protected $name = 'auth.login';
	
	/**
	 * LoginEvent constructor.
	 * @param User $user
	 */
	public function __construct(User $user)
	{
		$this->setTarget($user);
	}
	
	/**
	 * @return User
	 */
	public function getTarget(): User
	{
		return parent::getTarget();
	}
}
