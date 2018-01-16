<?php

namespace App\Auth;

use App\Account\Entity\User;
use App\Auth\Table\UserTable;
use Framework\Auth\AuthInterface;
use Framework\Auth\UserInterface;
use Framework\Database\NoRecordException;
use Framework\Session\SessionInterface;

class DatabaseAuth implements AuthInterface
{
    /**
     * @var UserTable
     */
    private $userTable;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UserInterface
     */
    private $user;
    
    /**
     * DatabaseAuth constructor.
     * @param UserTable        $userTable
     * @param SessionInterface $session
     */
    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session   = $session;
    }
    
    /**
     * @param string $username
     * @param string $password
     * @return UserInterface|null
     * @throws \Framework\Database\NoRecordException
     */
    public function login(string $username, string $password): ?UserInterface
    {
        if (empty($username) || empty($password)) {
            return null;
        }
        /** @var User $user */
        $user = $this->userTable->findBy('username', $username);
        if ($user && password_verify($password, $user->getPassword())) {
            $this->setUser($user);
            return $user;
        }
        return null;
    }
    
    /**
     * @return void
     */
    public function logout(): void
    {
        $this->session->delete('auth.user');
    }
    
    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        if ($this->user) {
            return $this->user;
        }
        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find($userId);
                return $this->user;
            } catch (NoRecordException $exception) {
                $this->session->delete('auth.user');
                return null;
            }
        }
        return null;
    }
    
    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        /** @var User $user */
        $this->session->set('auth.user', $user->getId());
        $this->user = $user;
    }
}
