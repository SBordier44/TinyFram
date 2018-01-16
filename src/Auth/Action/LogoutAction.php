<?php

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var DatabaseAuth
     */
    private $auth;
    /**
     * @var FlashService
     */
    private $flashService;
    
    public function __construct(RendererInterface $renderer, DatabaseAuth $auth, FlashService $flashService)
    {
        $this->renderer     = $renderer;
        $this->auth         = $auth;
        $this->flashService = $flashService;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->auth->logout();
        $this->flashService->success('Vous êtes maintenant déconnecté');
        return new RedirectResponse('/blog');
    }
}
