<?php

namespace App\Auth\Table;

use DateTime;
use Framework\Auth\UserInterface;
use Framework\Database\Table;
use Ramsey\Uuid\Uuid;
use const DATE_ATOM;
use const PASSWORD_DEFAULT;
use function password_hash;

class UserTable extends Table
{
	/**
	 * @var string
	 */
	protected $table = 'users';
	
	/**
	 * UserTable constructor.
	 * @param \PDO   $pdo
	 * @param string $entity
	 */
	public function __construct(\PDO $pdo, string $entity = UserInterface::class)
	{
		$this->entity = $entity;
		parent::__construct($pdo);
	}
	
	/**
	 * @param int $userId
	 * @return string
	 */
	public function resetPassword(int $userId): string
	{
		$token = Uuid::uuid4()->toString();
		$this->update($userId, [
			'password_reset'    => $token,
			'password_reset_at' => (new DateTime())->format(DATE_ATOM)
		]);
		return $token;
	}
	
	/**
	 * @param int    $userId
	 * @param string $password
	 */
	public function updatePassword(int $userId, string $password): void
	{
		$this->update($userId, [
			'password'          => password_hash($password, PASSWORD_DEFAULT),
			'password_reset'    => null,
			'password_reset_at' => null
		]);
	}
}
