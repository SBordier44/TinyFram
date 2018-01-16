<?php

namespace App\Account\Action;

use App\Account\Entity\User;
use App\Auth\Table\UserTable;
use Framework\Auth\AuthInterface;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use const PASSWORD_DEFAULT;
use function password_hash;

class AccountEditAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var AuthInterface
     */
    private $auth;
    /**
     * @var FlashService
     */
    private $flashService;
    /**
     * @var UserTable
     */
    private $userTable;
    
    /**
     * AccountEditAction constructor.
     * @param RendererInterface $renderer
     * @param AuthInterface     $auth
     * @param FlashService      $flashService
     * @param UserTable         $userTable
     */
    public function __construct(
        RendererInterface $renderer,
        AuthInterface $auth,
        FlashService $flashService,
        UserTable $userTable
    ) {
        $this->renderer     = $renderer;
        $this->auth         = $auth;
        $this->flashService = $flashService;
        $this->userTable    = $userTable;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        /** @var User $user */
        $user      = $this->auth->getUser();
        $params    = $request->getParsedBody();
        $validator = (new Validator($params))->required('firstname', 'firstname')->confirm('password');
        if ($validator->isValid()) {
            $userParams = [
                'firstname' => $params['firstname'],
                'lastname'  => $params['lastname']
            ];
            if (!empty($params['password'])) {
                $userParams['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            }
            $this->userTable->update($user->getId(), $userParams);
            $this->flashService->success('Votre compte a bien été mis à jour');
            return new RedirectResponse($request->getUri()->getPath());
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/account', compact('user', 'errors'));
    }
}
