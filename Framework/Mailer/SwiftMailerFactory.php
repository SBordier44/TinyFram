<?php

namespace Framework\Mailer;

use Psr\Container\ContainerInterface;
use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_SmtpTransport;

class SwiftMailerFactory
{
	/**
	 * @param ContainerInterface $container
	 * @return Swift_Mailer
	 * @throws \Psr\Container\ContainerExceptionInterface
	 */
	public function __invoke(ContainerInterface $container): Swift_Mailer
	{
		if ($container->get('env') === 'prod') {
			$transport = new Swift_SendmailTransport();
		} else {
			$transport = (new Swift_SmtpTransport($container->get('smtp.host'),
				$container->get('smtp.port')))->setAuthMode($container->get('smtp.auth_mode'))
											  ->setUsername($container->get('smtp.username'))
											  ->setPassword($container->get('smtp.password'));
		}
		return new Swift_Mailer($transport);
	}
}
