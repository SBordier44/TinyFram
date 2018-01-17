<?php

namespace Framework\Router;

use Psr\Container\ContainerInterface;

class RouterFactory
{
    /**
     * @param ContainerInterface $container
     * @return Router
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container)
    {
        $cache = null;
        if (!$container->get('debug')) {
            $cache = 'tmp/routes';
        }
        return new Router($cache);
    }
}
