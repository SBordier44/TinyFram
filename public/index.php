<?php

use App\Account\AccountModule;
use App\Admin\AdminModule;
use App\Auth\AuthModule;
use App\Auth\ForbiddenMiddleware;
use App\Basket\BasketModule;
use App\Blog\Actions\PostIndexAction;
use App\Blog\BlogModule;
use App\Contact\ContactModule;
use App\Shop\ShopModule;
use Dotenv\Dotenv;
use Framework\App;
use Framework\Auth\RoleMiddlewareFactory;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\RendererRequestMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;
use Framework\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Whoops;
use function Http\Response\send;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
(new Dotenv(dirname(__DIR__) . DIRECTORY_SEPARATOR))->overload();
$app       = (new App([
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/bootstrap.php',
    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config/app.php'
]))->addModule(AdminModule::class)
   ->addModule(ShopModule::class)
   ->addModule(BlogModule::class)
   ->addModule(AuthModule::class)
   ->addModule(ContactModule::class)
   ->addModule(AccountModule::class)
   ->addModule(BasketModule::class);
$container = $app->getContainer();
$container->get(Router::class)->get('/', PostIndexAction::class, 'home');
$app->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe($container->get('admin.prefix'), $container->get(RoleMiddlewareFactory::class)->makeForRole('admin'))
    ->pipe(MethodMiddleware::class)
    ->pipe(RendererRequestMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);
if (PHP_SAPI !== 'cli') {
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}
