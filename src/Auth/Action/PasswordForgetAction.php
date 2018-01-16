<?php

namespace App\Auth\Action;

use App\Auth\Entity\User;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\Table\UserTable;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class PasswordForgetAction
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var UserTable
	 */
	private $userTable;
	/**
	 * @var PasswordResetMailer
	 */
	private $mailer;
	/**
	 * @var FlashService
	 */
	private $flashService;
	
	/**
	 * Constructor of PasswordForgetAction
	 * @param RendererInterface   $renderer
	 * @param UserTable           $userTable
	 * @param PasswordResetMailer $mailer
	 * @param FlashService        $flashService
	 */
	public function __construct(
		RendererInterface $renderer,
		UserTable $userTable,
		PasswordResetMailer $mailer,
		FlashService $flashService
	) {
		
		$this->renderer     = $renderer;
		$this->userTable    = $userTable;
		$this->mailer       = $mailer;
		$this->flashService = $flashService;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return RedirectResponse|string
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		if ($request->getMethod() === 'GET') {
			return $this->renderer->render('@auth/forget-password');
		}
		$params    = $request->getParsedBody();
		$validator = (new Validator($params))->notEmpty('email')->email('email');
		if ($validator->isValid()) {
			try {
				/**
				 * @var User $user
				 */
				$user  = $this->userTable->findBy('email', $params['email']);
				$token = $this->userTable->resetPassword($user->getId());
				$this->mailer->send($user->getEmail(), [
					'user'  => $user,
					'token' => $token
				]);
				$this->flashService->success('Un Email vous a été envoyé');
				return new RedirectResponse($request->getUri()->getPath());
			} catch (NoRecordException $e) {
				$errors = ['email' => 'Adresse Email Inconnue'];
			}
		} else {
			$errors = $validator->getErrors();
		}
		return $this->renderer->render('@auth/forget-password', compact('errors'));
	}
}
