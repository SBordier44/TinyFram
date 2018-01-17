<?php

namespace Framework\Auth;

interface AuthInterface
{
    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;
}
