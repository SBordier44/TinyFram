<?php

use App\Auth\AuthTwigExtension;
use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\Table\UserTable;
use Framework\Auth\AuthInterface;
use Framework\Auth\UserInterface;
use function DI\add;
use function DI\factory;
use function DI\get;
use function DI\object;

return [
    'auth.login'               => '/login',
    'auth.account'             => '/account',
    'auth.entity'              => UserInterface::class,
    'twig.extensions'          => add([
        get(AuthTwigExtension::class)
    ]),
    UserInterface::class       => factory(function (AuthInterface $auth) {
        return $auth->getUser();
    })->parameter('auth', get(AuthInterface::class)),
    AuthInterface::class       => get(DatabaseAuth::class),
    UserTable::class           => object()->constructorParameter('entity', get('auth.entity')),
    ForbiddenMiddleware::class => object()
        ->constructorParameter('loginPath', get('auth.login'))
        ->constructorParameter('accountPath', get('auth.account')),
    PasswordResetMailer::class => object()->constructorParameter('from', get('mail.from'))
];
