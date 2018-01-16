<?php

namespace App\Contact;

use App\Contact\Action\ContactAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;

class ContactModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config.php';
    
    /**
     * Constructor of ContactModule
     * @param Router            $router
     * @param RendererInterface $renderer
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     */
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('contact', __DIR__ . '/views');
        $router->get('/contact', ContactAction::class, 'contact');
        $router->post('/contact', ContactAction::class);
    }
}
