<?php

namespace App\Auth\Mailer;

use Framework\Renderer\RendererInterface;
use Swift_Mailer;
use Swift_Message;

class PasswordResetMailer
{
	/**
	 * @var Swift_Mailer
	 */
	private $mailer;
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var string
	 */
	private $from;
	
	/**
	 * Constructor of PasswordResetMailer
	 * @param Swift_Mailer      $mailer
	 * @param RendererInterface $renderer
	 * @param string            $from
	 */
	public function __construct(Swift_Mailer $mailer, RendererInterface $renderer, string $from)
	{
		$this->mailer   = $mailer;
		$this->renderer = $renderer;
		$this->from     = $from;
	}
	
	/**
	 * @param string $to
	 * @param array  $params
	 * @return void
	 */
	public function send(string $to, array $params): void
	{
		$message = new Swift_Message('Réinitialisation de votre mot de passe',
			$this->renderer->render('@auth/email/forget-password.text', $params), 'text/plain');
		$message->addPart($this->renderer->render('@auth/email/forget-password.html', $params), 'text/html');
		$message->setTo($to);
		$message->setFrom($this->from);
		$this->mailer->send($message);
	}
}
