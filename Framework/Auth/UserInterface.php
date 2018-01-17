<?php

namespace Framework\Auth;

interface UserInterface
{
    /**
     * @return string|null
     */
    public function getUsername(): ?string;
    
    /**
     * @return array
     */
    public function getRoles(): array;
    
    /**
     * @return null|int
     */
    public function getId(): ?int;
}
