<?php

namespace Tests\Framework;

use Framework\Router\{
	Route, Router
};
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
	
	/**
	 * @var Router
	 */
	private $router;
	
	public function setUp()
	{
		$this->router = new Router();
	}
	
	public function testGetMethod()
	{
		$request = new ServerRequest('GET', '/blog');
		$this->router->get('/blog', function () {
			return 'hello';
		}, 'blog');
		$route = $this->router->match($request);
		static::assertEquals('blog', $route->getName());
		static::assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
	}
	
	public function testPostDeleteMethod()
	{
		$fake = function () {
			return 'hello';
		};
		$this->router->get('/blog', $fake, 'blog');
		$this->router->post('/blog', $fake, 'blog.post');
		$this->router->delete('/blog', $fake, 'blog.delete');
		static::assertEquals('blog', $this->router->match(new ServerRequest('GET', '/blog'))->getName());
		static::assertEquals('blog.post', $this->router->match(new ServerRequest('POST', '/blog'))->getName());
		static::assertEquals('blog.delete', $this->router->match(new ServerRequest('DELETE', '/blog'))->getName());
	}
	
	public function testCrudMethod()
	{
		$this->router->crud('/blog', function () {
		}, 'blog');
		static::assertEquals('blog.index', $this->router->match(new ServerRequest('GET', '/blog'))->getName());
		static::assertEquals('blog.create', $this->router->match(new ServerRequest('GET', '/blog/new'))->getName());
		static::assertInstanceOf(Route::class, $this->router->match(new ServerRequest('POST', '/blog/new')));
		static::assertEquals('blog.edit', $this->router->match(new ServerRequest('GET', '/blog/1'))->getName());
		static::assertInstanceOf(Route::class, $this->router->match(new ServerRequest('POST', '/blog/1')));
		static::assertInstanceOf(Route::class, $this->router->match(new ServerRequest('DELETE', '/blog/1')));
	}
	
	public function testGetMethodIfURLDoesNotExists()
	{
		$request = new ServerRequest('GET', '/blog');
		$this->router->get('/blogaze', function () {
			return 'hello';
		}, 'blog');
		$route = $this->router->match($request);
		static::assertEquals(null, $route);
	}
	
	public function testGetMethodWithParameters()
	{
		$request = new ServerRequest('GET', '/blog/mon-slug-8');
		$this->router->get('/blog', function () {
			return 'azezea';
		}, 'posts');
		$this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
			return 'hello';
		}, 'post.show');
		$route = $this->router->match($request);
		static::assertEquals('post.show', $route->getName());
		static::assertEquals('hello', call_user_func($route->getCallback(), $request));
		static::assertEquals(['slug' => 'mon-slug', 'id' => '8'], $route->getParams());
		$route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-8'));
		static::assertEquals(null, $route);
	}
	
	public function testGenerateUri()
	{
		$this->router->get('/blog', function () {
			return 'azezea';
		}, 'posts');
		$this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
			return 'hello';
		}, 'post.show');
		$uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => 18]);
		static::assertEquals('/blog/mon-article-18', $uri);
	}
	
	public function testGenerateUriWithQueryParams()
	{
		$this->router->get('/blog', function () {
			return 'azezea';
		}, 'posts');
		$this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
			return 'hello';
		}, 'post.show');
		$uri = $this->router->generateUri('post.show', ['slug' => 'mon-article', 'id' => 18], ['p' => 2]);
		static::assertEquals('/blog/mon-article-18?p=2', $uri);
	}
}
