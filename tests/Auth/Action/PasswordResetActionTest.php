<?php

namespace Tests\Auth\Action;

use App\Auth\Action\{
	PasswordForgetAction, PasswordResetAction
};
use App\Auth\Entity\User;
use App\Auth\Table\UserTable;
use DateInterval;
use DateTime;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;
use function call_user_func;

class PasswordResetActionTest extends ActionTestCase
{
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
		$this->renderer = $this->prophesize(RendererInterface::class);
		$this->renderer->render(Argument::cetera())->willReturnArgument();
		$this->userTable = $this->prophesize(UserTable::class);
		$router          = $this->prophesize(Router::class);
		$router->generateUri(Argument::cetera())->willReturnArgument();
		$this->action = new PasswordResetAction($this->renderer->reveal(), $this->userTable->reveal(),
			$this->prophesize(FlashService::class)->reveal(), $router->reveal());
	}
	
	public function testWithBadToken()
	{
		$user    = $this->makeUser();
		$request = $this->makeRequest('/da')
						->withAttribute('id', $user->getId())
						->withAttribute('token', $user->getPasswordReset() . 'aze');
		$this->userTable->find($user->getId())->willReturn($user);
		$response = call_user_func($this->action, $request);
		$this->assertRedirect($response, 'auth.forget_password');
	}
	
	private function makeUser(): User
	{
		$user = new User();
		$user->setId(3);
		$user->setPasswordReset('fake');
		$user->setPasswordResetAt(new DateTime());
		return $user;
	}
	
	public function testWithExpiredToken()
	{
		$user = $this->makeUser();
		$user->setPasswordResetAt((new DateTime())->sub(new DateInterval('PT15M')));
		$request = $this->makeRequest('/da')
						->withAttribute('id', $user->getId())
						->withAttribute('token', $user->getPasswordReset() . 'aze');
		$this->userTable->find($user->getId())->willReturn($user);
		$response = call_user_func($this->action, $request);
		$this->assertRedirect($response, 'auth.forget_password');
	}
	
	public function testWithValidToken()
	{
		$user = $this->makeUser();
		$user->setPasswordResetAt((new DateTime())->sub(new DateInterval('PT5M')));
		$request = $this->makeRequest('/da')
						->withAttribute('id', $user->getId())
						->withAttribute('token', $user->getPasswordReset());
		$this->userTable->find($user->getId())->willReturn($user);
		$response = call_user_func($this->action, $request);
		static::assertEquals($response, '@auth/reset-password');
	}
	
	public function testPostWithBadPassword()
	{
		$user = $this->makeUser();
		$user->setPasswordResetAt((new DateTime())->sub(new DateInterval('PT5M')));
		$request = $this->makeRequest('/da', [
			'password'         => '00000000',
			'password_confirm' => '0000'
		])->withAttribute('id', $user->getId())->withAttribute('token', $user->getPasswordReset());
		$this->userTable->find($user->getId())->willReturn($user);
		$this->renderer->render(Argument::type('string'), Argument::withKey('errors'))
					   ->shouldBeCalled()
					   ->willReturnArgument();
		$response = call_user_func($this->action, $request);
		static::assertEquals($response, '@auth/reset-password');
	}
	
	public function testPostWithGoodPassword()
	{
		$user = $this->makeUser();
		$user->setPasswordResetAt((new DateTime())->sub(new DateInterval('PT5M')));
		$request = $this->makeRequest('/da', [
			'password'         => '00000000',
			'password_confirm' => '00000000'
		])->withAttribute('id', $user->getId())->withAttribute('token', $user->getPasswordReset());
		$this->userTable->find($user->getId())->willReturn($user);
		$this->userTable->updatePassword($user->getId(), '00000000')->shouldBeCalled();
		$response = call_user_func($this->action, $request);
		$this->assertRedirect($response, 'auth.login');
	}
}
