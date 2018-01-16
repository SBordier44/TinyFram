<?php

namespace Tests\Account\Action;

use App\Account\Action\AccountEditAction;
use App\Account\Entity\User;
use App\Auth\Table\UserTable;
use Framework\Auth\AuthInterface;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\ActionTestCase;
use function array_keys;
use function call_user_func;

class AccountEditActionTest extends ActionTestCase
{
    /**
     * @var ObjectProphecy
     */
    private $renderer;
    /**
     * @var AccountEditAction
     */
    private $action;
    /**
     * @var ObjectProphecy
     */
    private $auth;
    /**
     * @var User
     */
    private $user;
    /**
     * @var ObjectProphecy
     */
    private $userTable;
    
    public function setUp()
    {
        parent::setUp();
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->user     = new User();
        $this->user->setId(3);
        $this->auth = $this->prophesize(AuthInterface::class);
        $this->auth->getUser()->willReturn($this->user);
        $this->userTable = $this->prophesize(UserTable::class);
        $this->action    = new AccountEditAction(
            $this->renderer->reveal(),
            $this->auth->reveal(),
            $this->prophesize(FlashService::class)->reveal(),
            $this->userTable->reveal()
        );
    }
    
    public function testValid()
    {
        $this->userTable->update(3, [
            'firstname' => 'John',
            'lastname'  => 'Doe'
        ])->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname' => 'John',
            'lastname'  => 'Doe'
        ]));
        $this->assertRedirect($response, '/demo');
    }
    
    public function testValidWithPassword()
    {
        $this->userTable->update(3, Argument::that(function ($params) {
            self::assertEquals(['firstname', 'lastname', 'password'], array_keys($params));
            return true;
        }))->shouldBeCalled();
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname'        => 'John',
            'lastname'         => 'Doe',
            'password'         => '00000000',
            'password_confirm' => '00000000'
        ]));
        $this->assertRedirect($response, '/demo');
    }
    
    public function testPostInvalid()
    {
        $this->userTable->update(3, [])->shouldNotBeCalled();
        $this->renderer->render('@account/account', Argument::that(function ($params) {
            static::assertEquals(['password'], array_keys($params['errors']));
            return true;
        }));
        $response = call_user_func($this->action, $this->makeRequest('/demo', [
            'firstname'        => 'John',
            'lastname'         => 'Doe',
            'password'         => '00000000',
            'password_confirm' => '0000'
        ]));
    }
}
