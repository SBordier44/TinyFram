<?php

namespace Tests\Auth\Action;

use App\Auth\Action\PasswordForgetAction;
use App\Auth\Entity\User;
use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\Table\UserTable;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;
use function call_user_func;

class PasswordForgetActionTest extends ActionTestCase
{
    /**
     * @var ObjectProphecy
     */
    private $mailer;
    /**
     * @var ObjectProphecy
     */
    private $userTable;
    /**
     * @var ObjectProphecy
     */
    private $renderer;
    /**
     * @var PasswordForgetAction
     */
    private $action;
    
    public function setUp()
    {
        $this->renderer  = $this->prophesize(RendererInterface::class);
        $this->userTable = $this->prophesize(UserTable::class);
        $this->mailer    = $this->prophesize(PasswordResetMailer::class);
        $this->action    = new PasswordForgetAction(
            $this->renderer->reveal(),
            $this->userTable->reveal(),
            $this->mailer->reveal(),
            $this->prophesize(FlashService::class)->reveal()
        );
    }
    
    public function testEmailInvalid()
    {
        $request = $this->makeRequest('/demo', ['email' => 'azeaze']);
        $this->renderer->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
                       ->shouldBeCalled()
                       ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        static::assertEquals('@auth/forget-password', $response);
    }
    
    public function testEmailDontExists()
    {
        $request = $this->makeRequest('/demo', ['email' => 'john@doe.fr']);
        $this->userTable->findBy('email', 'john@doe.fr')->willThrow(new NoRecordException());
        $this->renderer->render(Argument::type('string'), Argument::withEntry('errors', Argument::withKey('email')))
                       ->shouldBeCalled()
                       ->willReturnArgument();
        $response = call_user_func($this->action, $request);
        static::assertEquals('@auth/forget-password', $response);
    }
    
    public function testWithGoodEmail()
    {
        $user = new User();
        $user->setId(3);
        $user->setEmail('john@doe.fr');
        $token   = 'fake';
        $request = $this->makeRequest('/demo', ['email' => $user->getEmail()]);
        $this->userTable->findBy('email', $user->getEmail())->willReturn($user);
        $this->userTable->resetPassword(3)->willReturn($token);
        $this->mailer->send($user->getEmail(), [
            'user'  => $user,
            'token' => $token
        ])->shouldBeCalled();
        $this->renderer->render('')->shouldNotBeCalled();
        $response = call_user_func($this->action, $request);
        $this->assertRedirect($response, '/demo');
    }
}
