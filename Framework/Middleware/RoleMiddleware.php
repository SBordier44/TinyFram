<?php

namespace Framework\Middleware;

use Framework\Auth\AuthInterface;
use Framework\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;
    /**
     * @var string
     */
    private $role;
    
    /**
     * RoleMiddleware constructor.
     * @param AuthInterface $auth
     * @param string        $role
     */
    public function __construct(AuthInterface $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $user = $this->auth->getUser();
        if ($user === null || !\in_array($this->role, $user->getRoles(), true)) {
            throw new ForbiddenException('');
        }
        return $delegate->handle($request);
    }
}
