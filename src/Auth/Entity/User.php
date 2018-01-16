<?php

namespace App\Auth\Entity;

use DateTime;
use Framework\Auth\UserInterface;
use function is_string;

class User implements UserInterface
{
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var string|null
     */
    private $email;
    /**
     * @var string|null
     */
    private $password;
    /**
     * @var string|null
     */
    private $passwordReset;
    /**
     * @var DateTime|string|null
     */
    private $passwordResetAt;
    
    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }
    
    /**
     * @param string|null $username
     * @return User
     */
    public function setUsername(?string $username): User
    {
        $this->username = $username;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getRoles(): array
    {
        return [];
    }
    
    /**
     * @return string|null
     */
    public function getPasswordReset(): ?string
    {
        return $this->passwordReset;
    }
    
    /**
     * @param null|string $passwordReset
     * @return UserInterface
     */
    public function setPasswordReset(?string $passwordReset): UserInterface
    {
        $this->passwordReset = $passwordReset;
        return $this;
    }
    
    /**
     * @return DateTime|null|string
     */
    public function getPasswordResetAt()
    {
        return $this->passwordResetAt;
    }
    
    /**
     * @param string|DateTime $datetime
     * @return UserInterface
     */
    public function setPasswordResetAt($datetime): UserInterface
    {
        $this->passwordResetAt = is_string($datetime) ? new DateTime($datetime) : $datetime;
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
     * @return User
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
    
    /**
     * @param string|null $password
     * @return User
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }
}
