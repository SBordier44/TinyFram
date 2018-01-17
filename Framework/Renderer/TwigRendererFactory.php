<?php

namespace Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Extension\DebugExtension;

class TwigRendererFactory
{
    /**
     * @param ContainerInterface $container
     * @return TwigRenderer
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $debug    = $container->get('env') !== 'prod';
        $viewPath = $container->get('views.path');
        $loader   = new \Twig_Loader_Filesystem($viewPath);
        $twig     = new \Twig_Environment($loader, [
            'debug'       => $debug,
            'cache'       => $debug ? false : 'tmp/views',
            'auto_reload' => $debug
        ]);
        $twig->addExtension(new DebugExtension());
        if ($container->has('twig.extensions')) {
            $exts = $container->get('twig.extensions');
            foreach ($exts as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($twig);
    }
}
