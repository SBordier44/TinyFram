<?php

namespace Framework;

use DI\ContainerBuilder;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\FilesystemCache;
use Framework\Middleware\CombinedMiddleware;
use Framework\Middleware\RoutePrefixedMiddleware;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class App implements RequestHandlerInterface
{
    /**
     * @var array
     */
    private $modules = [];
    /**
     * @var array
     */
    private $definitions;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $middlewares = [];
    /**
     * @var int
     */
    private $index = 0;
    
    /**
     * App constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions = [])
    {
        if (!$this->isSequential($definitions)) {
            $definitions = [$definitions];
        }
        $this->definitions = $definitions;
    }
    
    /**
     * @param array $array
     * @return bool
     */
    private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, \count($array) - 1);
    }
    
    /**
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }
    
    /**
     * @param string|callable|MiddlewareInterface      $routePrefix
     * @param null|string|callable|MiddlewareInterface $middleware
     * @return App
     * @throws \InvalidArgumentException
     */
    public function pipe($routePrefix, $middleware = null): self
    {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }
    
    /**
     * @return ContainerInterface
     * @throws \InvalidArgumentException
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $debug   = getenv('APP_ENV') === 'dev';
            if (!$debug) {
                if (\function_exists('apcu_cache_info')) {
                    $builder->setDefinitionCache(new ApcuCache());
                } else {
                    $builder->setDefinitionCache(new FilesystemCache(\dirname(__DIR__) . '/tmp/di'));
                }
                $builder->writeProxiesToFile(true, 'tmp/di/proxies');
            }
            foreach ($this->definitions as $definition) {
                $builder->addDefinitions($definition);
            }
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $builder->addDefinitions([
                __CLASS__ => $this
            ]);
            $this->container = $builder->build();
        }
        return $this->container;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \Exception
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \InvalidArgumentException
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->buildEnv();
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }
    
    /**
     * @return void
     * @throws \RuntimeException
     */
    public function buildEnv(): void
    {
        if (getenv('APP_ENV') === 'dev') {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        } else {
            if (!@mkdir('tmp') && !is_dir('tmp')) {
                throw new RuntimeException('Unable to create the <strong>tmp/</strong> directory');
            }
            error_reporting(0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Exception
     * @throws \LogicException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;
        if ($this->index > 1) {
            throw new LogicException('');
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);
        return $middleware->process($request, $this);
    }
    
    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
