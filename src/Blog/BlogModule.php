<?php

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    public const MIGRATIONS  = __DIR__ . '/db/migrations';
    public const SEEDS       = __DIR__ . '/db/seeds';
    
    /**
     * BlogModule constructor.
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($container->get('blog.prefix'), PostIndexAction::class, 'blog.index');
        $router->get("$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}", PostShowAction::class, 'blog.show');
        $router->get("$blogPrefix/category/{slug:[a-z\-0-9]+}", CategoryShowAction::class, 'blog.category');
        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
