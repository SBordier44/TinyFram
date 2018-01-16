<?php

namespace App\Auth\Action;

use App\Account\Entity\User;
use App\Auth\DatabaseAuth;
use App\Auth\Event\LoginEvent;
use Framework\Actions\RouterAwareAction;
use Framework\Event\EventManager;
use Framework\Event\Psr\EventManagerInterface;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router\Router;
use Framework\Session\{
	FlashService, SessionInterface
};
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\RouterInterface;

class LoginAttemptAction
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
	 * @var SessionInterface
	 */
	private $session;
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var EventManagerInterface
	 */
	private $eventManager;
	
	use RouterAwareAction;
	
	/**
	 * LoginAttemptAction constructor.
	 * @param RendererInterface $renderer
	 * @param DatabaseAuth      $auth
	 * @param Router            $router
	 * @param SessionInterface  $session
	 * @param EventManager      $eventManager
	 */
	public function __construct(
		RendererInterface $renderer,
		DatabaseAuth $auth,
		Router $router,
		SessionInterface $session,
		EventManager $eventManager
	) {
		$this->renderer     = $renderer;
		$this->auth         = $auth;
		$this->router       = $router;
		$this->session      = $session;
		$this->eventManager = $eventManager;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return RedirectResponse|\Psr\Http\Message\ResponseInterface
	 * @throws \Framework\Database\NoRecordException
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$params = $request->getParsedBody();
		/** @var User $user */
		$user = $this->auth->login($params['username'], $params['password']);
		if ($user) {
			$this->eventManager->trigger(new LoginEvent($user));
			$path = $this->session->get('auth.redirect') ? : $this->router->generateUri($user->getRole() === 'admin' ? 'admin' : 'account');
			$this->session->delete('auth.redirect');
			return new RedirectResponse($path);
		}
		(new FlashService($this->session))->error('Identifiant ou mot de passe incorrect');
		return $this->redirect('auth.login');
	}
}
