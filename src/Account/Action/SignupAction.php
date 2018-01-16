<?php

namespace App\Account\Action;

use App\Account\Entity\User;
use App\Auth\DatabaseAuth;
use App\Auth\Table\UserTable;
use Framework\Database\Hydrator;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use const PASSWORD_DEFAULT;
use function password_hash;

class SignupAction
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
     * @var DatabaseAuth
     */
    private $auth;
    /**
     * @var FlashService
     */
    private $flash;
    
    /**
     * SignupAction constructor.
     * @param RendererInterface $renderer
     * @param UserTable         $userTable
     * @param Router            $router
     * @param DatabaseAuth      $auth
     * @param FlashService      $flash
     */
    public function __construct(
        RendererInterface $renderer,
        UserTable $userTable,
        Router $router,
        DatabaseAuth $auth,
        FlashService $flash
    ) {
        $this->renderer  = $renderer;
        $this->userTable = $userTable;
        $this->router    = $router;
        $this->auth      = $auth;
        $this->flash     = $flash;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return RedirectResponse|string
     * @throws \Zend\Expressive\Router\Exception\InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@account/signup');
        }
        $params    = $request->getParsedBody();
        $validator = (new Validator($params))->required('username', 'email', 'password', 'password_confirm')
                                             ->length('username', 5)
                                             ->email('email')
                                             ->confirm('password')
                                             ->length('password', 4)
                                             ->unique('username', $this->userTable)
                                             ->unique('email', $this->userTable);
        if ($validator->isValid()) {
            $userParams = [
                'username' => $params['username'],
                'email'    => $params['email'],
                'password' => password_hash($params['password'], PASSWORD_DEFAULT)
            ];
            $this->userTable->insert($userParams);
            $user     = Hydrator::hydrate($userParams, User::class);
            $user->id = $this->userTable->getPdo()->lastInsertId();
            $this->auth->setUser($user);
            $this->flash->success('Votre compte a bien été créé');
            return new RedirectResponse($this->router->generateUri('account'));
        }
        return $this->renderer->render('@account/signup', [
            'errors' => $validator->getErrors(),
            'user'   => [
                'username' => $params['username'],
                'email'    => $params['email']
            ]
        ]);
    }
}
