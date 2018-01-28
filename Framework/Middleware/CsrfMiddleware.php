<?php

namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class CsrfMiddleware implements MiddlewareInterface
{
    
    /**
     * @var string
     */
    private $formKey;
    /**
     * @var string
     */
    private $sessionKey;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var \ArrayAccess
     */
    private $session;
    
    public function __construct(
        &$session,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    ) {
        $this->validSession($session);
        $this->session    = &$session;
        $this->formKey    = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit      = $limit;
    }
    
    /**
     * @param $session
     * @throws \RuntimeException
     */
    private function validSession($session)
    {
        if (!\is_array($session) && !$session instanceof \ArrayAccess) {
            throw new RuntimeException('La session passé au middleware CSRF n\'est pas traitable comme un tableau');
        }
    }
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     * @throws CsrfInvalidException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if (\in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'], true)) {
            $params = $request->getParsedBody() ? : [];
            if (array_key_exists($this->formKey, $params)) {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (\in_array($params[$this->formKey], $csrfList, true)) {
                    $this->useToken($params[$this->formKey]);
                    return $delegate->handle($request);
                }
                $this->reject();
            } else {
                $this->reject();
            }
        }
        return $delegate->handle($request);
    }
    
    /**
     * @param $token
     */
    private function useToken($token): void
    {
        $tokens                           = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }
    
    /**
     * @throws CsrfInvalidException
     */
    private function reject(): void
    {
        throw new CsrfInvalidException('');
    }
    
    /**
     * @return string
     */
    public function generateToken(): string
    {
        $token                            = bin2hex(random_bytes(16));
        $csrfList                         = $this->session[$this->sessionKey] ?? [];
        $csrfList[]                       = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }
    
    /**
     * @return void
     */
    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (\count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }
    
    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }
}
