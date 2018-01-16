<?php

namespace App\Auth;

use App\Auth\Action\LoginAction;
use App\Auth\Action\LoginAttemptAction;
use App\Auth\Action\LogoutAction;
use App\Auth\Action\PasswordForgetAction;
use App\Auth\Action\PasswordResetAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Psr\Container\ContainerInterface;

class AuthModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const MIGRATIONS  = __DIR__ . '/db/migrations';
    public const SEEDS       = __DIR__ . '/db/seeds';
    
    /**
     * Constructor of AuthModule
     * @param ContainerInterface $container
     * @param Router             $router
     * @param RendererInterface  $renderer
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('auth', __DIR__ . '/views');
        $router->get($container->get('auth.login'), LoginAction::class, 'auth.login');
        $router->post($container->get('auth.login'), LoginAttemptAction::class);
        $router->post('/logout', LogoutAction::class, 'auth.logout');
        $router->any('/forget-password', PasswordForgetAction::class, 'auth.forget_password');
        $router->any(
            '/forget-password/reset/{id:\d+}/{token}',
            PasswordResetAction::class,
            'auth.forget_password.reset'
        );
    }
}
