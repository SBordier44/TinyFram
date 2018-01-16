<?php

namespace App\Auth;

use Framework\Auth\AuthInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class AuthTwigExtension extends Twig_Extension
{
    /**
     * @var AuthInterface
     */
    private $auth;
    
    /**
     * AuthTwigExtension constructor.
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }
    
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('current_user', [$this->auth, 'getUser'])
        ];
    }
}
