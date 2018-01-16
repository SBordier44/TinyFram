<?php

namespace App\Contact\Action;

use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Swift_Mailer;
use Swift_Message;

class ContactAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var string
     */
    private $to;
    /**
     * @var FlashService
     */
    private $flash;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    
    /**
     * Constructor of ContactAction
     * @param string            $to
     * @param RendererInterface $renderer
     * @param FlashService      $flash
     * @param Swift_Mailer      $mailer
     */
    public function __construct(
        string $to,
        RendererInterface $renderer,
        FlashService $flash,
        Swift_Mailer $mailer
    ) {
        $this->renderer = $renderer;
        $this->to       = $to;
        $this->flash    = $flash;
        $this->mailer   = $mailer;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@contact/contact');
        }
        $params    = $request->getParsedBody();
        $validator = (new Validator($request->getParsedBody()))->required('name', 'email', 'content')
                                                               ->length('name', 5)
                                                               ->email('email')
                                                               ->length('content', 15);
        if ($validator->isValid()) {
            $this->flash->success('Merci pour votre Email');
            $message = new Swift_Message('Formulaire de contact');
            $message->setBody($this->renderer->render('@contact/email/contact.text', $params), 'text/plain', 'utf-8');
            $message->addPart($this->renderer->render('@contact/email/contact.html', $params), 'text/html', 'utf-8');
            $message->setTo($this->to, 'Site Webmaster');
            $message->setFrom($params['email'], $params['name']);
            $this->mailer->send($message);
            return new RedirectResponse((string)$request->getUri());
        }
        $this->flash->error('Votre saisie comporte des erreurs');
        $errors = $validator->getErrors();
        return $this->renderer->render('@contact/contact', compact('errors'));
    }
}
