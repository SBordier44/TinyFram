<?php

namespace Tests\Contact\Action;

use App\Contact\Action\ContactAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Swift_Mailer;
use Swift_Message;
use Tests\ActionTestCase;
use function call_user_func;

class ContactActionTest extends ActionTestCase
{
	/**
	 * @var FlashService
	 */
	private $flash;
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var ContactAction
	 */
	private $action;
	
	/**
	 * @var Swift_Mailer
	 */
	private $mailer;
	
	/**
	 * @var string
	 */
	private $to = 'demo@demo.fr';
	
	public function setUp()
	{
		$this->renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
		$this->flash    = $this->getMockBuilder(FlashService::class)->disableOriginalConstructor()->getMock();
		$this->mailer   = $this->getMockBuilder(Swift_Mailer::class)->disableOriginalConstructor()->getMock();
		$this->action   = new ContactAction($this->to, $this->renderer, $this->flash, $this->mailer);
	}
	
	public function testGet()
	{
		$this->renderer->expects(self::once())->method('render')->with('@contact/contact')->willReturn('');
		call_user_func($this->action, $this->makeRequest('/contact'));
	}
	
	public function testPostInvalid()
	{
		$request = $this->makeRequest('/contact', [
			'name'    => 'Jean marc',
			'email'   => 'azeaze',
			'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
		]);
		$this->renderer->expects(self::once())
					   ->method('render')
					   ->with('@contact/contact', self::callback(function ($params) {
						   static::assertArrayHasKey('errors', $params);
						   self::assertArrayHasKey('email', $params['errors']);
						   return true;
					   }))
					   ->willReturn('');
		$this->flash->expects(self::once())->method('error');
		call_user_func($this->action, $request);
	}
	
	public function testPostValid()
	{
		$request = $this->makeRequest('/contact', [
			'name'    => 'Jean marc',
			'email'   => 'demo@local.dev',
			'content' => 'zezeefdgefzfezfvezz'
		]);
		$this->flash->expects(self::once())->method('success');
		$this->mailer->expects(self::once())->method('send')->with(self::callback(function (Swift_Message $message) {
				self::assertArrayHasKey($this->to, $message->getTo());
				self::assertArrayHasKey('demo@local.dev', $message->getFrom());
				self::assertContains('texttexttext', $message->toString());
				self::assertContains('htmlhtmlhtml', $message->toString());
				return true;
			}));
		$this->renderer->expects(self::any())->method('render')->willReturn('texttexttext', 'htmlhtmlhtml');
		$response = call_user_func($this->action, $request);
		self::assertInstanceOf(RedirectResponse::class, $response);
	}
}
