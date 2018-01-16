<?php

namespace App\Auth;

use Framework\Auth\AuthInterface;
use Framework\Auth\UserInterface;
use Framework\Exception\ForbiddenException;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TypeError;

class ForbiddenMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $loginPath;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var AuthInterface
     */
    private $auth;
    /**
     * @var string
     */
    private $accountPath;
    
    /**
     * ForbiddenMiddleware constructor.
     * @param string           $loginPath
     * @param string           $accountPath
     * @param SessionInterface $session
     * @param AuthInterface    $auth
     */
    public function __construct(string $loginPath, string $accountPath, SessionInterface $session, AuthInterface $auth)
    {
        $this->loginPath   = $loginPath;
        $this->session     = $session;
        $this->auth        = $auth;
        $this->accountPath = $accountPath;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     * @throws TypeError
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        try {
            return $delegate->handle($request);
        } catch (ForbiddenException $exception) {
            return $this->redirectLogin($request);
        } catch (TypeError $error) {
            if (strpos($error->getMessage(), UserInterface::class) !== false) {
                return $this->redirectLogin($request);
            }
            throw $error;
        }
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vous devez posséder un compte pour accéder à cette page');
        return new RedirectResponse($this->loginPath);
    }
    
    /**
     * @return ResponseInterface
     */
    public function redirectBadRole(): ResponseInterface
    {
        (new FlashService($this->session))->error("Vous n'avez pas l'authorisation d'accéder à la page demandée");
        return new RedirectResponse($this->accountPath);
    }
}
