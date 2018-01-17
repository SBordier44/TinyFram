<?php

namespace Framework\Session;

class FlashService
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var string
     */
    private $sessionKey = 'flash';
    /**
     * @var array|string
     */
    private $messages;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    /**
     * @param string $message
     */
    public function success(string $message): void
    {
        $flash            = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }
    
    /**
     * @param string $message
     */
    public function error(string $message): void
    {
        $flash          = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }
    
    /**
     * @param string $type
     * @return null|string
     */
    public function get(string $type): ?string
    {
        if (null === $this->messages) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }
}
