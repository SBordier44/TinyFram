<?php

namespace App\Auth\Action;

use App\Auth\Entity\User;
use App\Auth\Table\UserTable;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class PasswordResetAction
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
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flashService;
    
    /**
     * PasswordResetAction constructor.
     * @param RendererInterface $renderer
     * @param UserTable         $userTable
     * @param FlashService      $flashService
     * @param Router            $router
     */
    public function __construct(
        RendererInterface $renderer,
        UserTable $userTable,
        FlashService $flashService,
        Router $router
    ) {
        $this->renderer     = $renderer;
        $this->userTable    = $userTable;
        $this->router       = $router;
        $this->flashService = $flashService;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse|string
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        /**
         * @var User $user
         */
        $user = $this->userTable->find($request->getAttribute('id'));
        if ($user->getPasswordReset() === $request->getAttribute('token') && time() - $user->getPasswordResetAt()
                                                                                           ->getTimestamp() < 600) {
            if ($request->getMethod() === 'GET') {
                return $this->renderer->render('@auth/reset-password');
            }
            $params    = $request->getParsedBody();
            $validator = (new Validator($params))->length('password', 8)->confirm('password');
            if ($validator->isValid()) {
                $this->userTable->updatePassword($user->getId(), $params['password']);
                $this->flashService->success('Votre mot de passe a bien été changé');
                return new RedirectResponse($this->router->generateUri('auth.login'));
            }
            $errors = $validator->getErrors();
            return $this->renderer->render('@auth/reset-password', compact('errors'));
        }
        $this->flashService->error('Token Invalide ou Expiré');
        return new RedirectResponse($this->router->generateUri('auth.forget_password'));
    }
}
