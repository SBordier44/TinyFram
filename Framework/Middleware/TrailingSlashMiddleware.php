<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class TrailingSlashMiddleware
{
	/**
	 * @param ServerRequestInterface $request
	 * @param callable               $next
	 * @return \GuzzleHttp\Psr7\MessageTrait|static
	 */
	public function __invoke(ServerRequestInterface $request, callable $next)
	{
		$uri = $request->getUri()->getPath();
		if (!empty($uri) && $uri !== '/' && $uri[-1] === '/') {
			return (new Response())->withStatus(301)->withHeader('Location', substr($uri, 0, -1));
		}
		return $next($request);
	}
}
