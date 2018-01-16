<?php

namespace Tests\Account\Action;

use App\Account\Action\SignupAction;
use App\Auth\DatabaseAuth;
use App\Auth\Table\UserTable;
use Framework\Auth\UserInterface;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use PDO;
use PDOStatement;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;
use function array_keys;
use function call_user_func;
use function password_verify;

class SignupActionTest extends ActionTestCase
{
	/**
	 * @var ObjectProphecy
	 */
	private $flashService;
	/**
	 * @var ObjectProphecy
	 */
	private $auth;
	/**
	 * @var ObjectProphecy
	 */
	private $router;
	/**
	 * @var ObjectProphecy
	 */
	private $userTable;
	/**
	 * @var ObjectProphecy
	 */
	private $renderer;
	/**
	 * @var SignupAction
	 */
	private $action;
	
	public function setUp()
	{
		parent::setUp();
		$this->userTable = $this->prophesize(UserTable::class);
		// UserTable
		$pdo       = $this->prophesize(PDO::class);
		$statement = $this->getMockBuilder(PDOStatement::class)->getMock();
		$statement->expects(self::any())->method('fetchColumn')->willReturn(false);
		$pdo->prepare(Argument::any())->willReturn($statement);
		$pdo->lastInsertId()->willReturn(3);
		$this->userTable->getTable()->willReturn('fake');
		$this->userTable->getPdo()->willReturn($pdo->reveal());
		// Renderer
		$this->renderer = $this->prophesize(RendererInterface::class);
		$this->renderer->render(Argument::any(), Argument::any())->willReturn('');
		// Router
		$this->router = $this->prophesize(Router::class);
		$this->router->generateUri(Argument::any())->will(function ($args) {
			return $args[0];
		});
		// Flash
		$this->flashService = $this->prophesize(FlashService::class);
		// Auth
		$this->auth   = $this->prophesize(DatabaseAuth::class);
		$this->action = new SignupAction($this->renderer->reveal(), $this->userTable->reveal(), $this->router->reveal(),
			$this->auth->reveal(), $this->flashService->reveal());
	}
	
	public function testGet()
	{
		call_user_func($this->action, $this->makeRequest());
		$this->renderer->render('@account/signup')->shouldHaveBeenCalled();
	}
	
	public function testPostInvalid()
	{
		call_user_func($this->action, $this->makeRequest('/demo', [
			'username'         => 'John Doe',
			'email'            => 'azeaze',
			'password'         => '00000000',
			'password_confirm' => '0000'
		]));
		$this->renderer->render('@account/signup', Argument::that(function ($params) {
			static::assertArrayHasKey('errors', $params);
			static::assertEquals(['email', 'password'], array_keys($params['errors']));
			return true;
		}))->shouldHaveBeenCalled();
	}
	
	public function testPostWithNoPassword()
	{
		call_user_func($this->action, $this->makeRequest('/demo', [
			'username'         => 'John Doe',
			'email'            => 'azeaze',
			'password'         => '',
			'password_confirm' => ''
		]));
		$this->renderer->render('@account/signup', Argument::that(function ($params) {
			static::assertArrayHasKey('errors', $params);
			static::assertEquals(['email', 'password'], array_keys($params['errors']));
			return true;
		}))->shouldHaveBeenCalled();
	}
	
	public function testPostValid()
	{
		$this->userTable->insert(Argument::that(function (array $userParams) {
			static::assertArraySubset([
				'username' => 'John Doe',
				'email'    => 'john@doe.fr'
			], $userParams);
			static::assertTrue(password_verify('00000000', $userParams['password']));
			return true;
		}))->shouldBeCalled();
		$this->auth->setUser(Argument::that(function (UserInterface $user) {
			$this->assertEquals('John Doe', $user->getUsername());
			$this->assertEquals('john@doe.fr', $user->getEmail());
			$this->assertEquals(3, $user->id);
			return true;
		}))->shouldBeCalled();
		$this->renderer->render('')->shouldNotBeCalled();
		$this->flashService->success(Argument::type('string'))->shouldBeCalled();
		$response = call_user_func($this->action, $this->makeRequest('/demo', [
			'username'         => 'John Doe',
			'email'            => 'john@doe.fr',
			'password'         => '00000000',
			'password_confirm' => '00000000'
		]));
		$this->assertRedirect($response, 'account');
	}
}
