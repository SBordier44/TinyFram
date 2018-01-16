<?php

namespace Tests;

use GuzzleHttp\Psr7\{
	ServerRequest, Uri
};
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ActionTestCase extends TestCase
{
	/**
	 * @param string $path
	 * @param array  $params
	 * @return ServerRequest|static
	 */
	protected function makeRequest(string $path = '', array $params = [])
	{
		$method = empty($params) ? 'GET' : 'POST';
		return (new ServerRequest($method, new Uri($path)))->withParsedBody($params);
	}
	
	/**
	 * @param ResponseInterface $response
	 * @param string            $path
	 * @return void
	 */
	protected function assertRedirect(ResponseInterface $response, string $path): void
	{
		self::assertSame(301, $response->getStatusCode());
		self::assertEquals([$path], $response->getHeader('Location'));
	}
}
