<?php

namespace App\Basket;

use App\Auth\Entity\User;
use App\Basket\Helper\DatabaseBasketHelper;
use App\Basket\Helper\SessionBasketHelper;
use App\Basket\Table\BasketTable;
use Framework\Auth\AuthInterface;
use Psr\Container\ContainerInterface;

class BasketFactory
{
    /**
     * @param ContainerInterface $container
     * @return DatabaseBasketHelper|SessionBasketHelper|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var User $user */
        $user = $container->get(AuthInterface::class)->getUser();
        if ($user) {
            return new DatabaseBasketHelper($user->getId(), $container->get(BasketTable::class));
        }
        return $container->get(SessionBasketHelper::class);
    }
}
