<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundMiddleware
{
	/**
	 * @param ServerRequestInterface $request
	 * @param callable               $next
	 * @return Response
	 */
	public function __invoke(ServerRequestInterface $request, callable $next)
	{
		return new Response(404, [], 'Erreur 404');
	}
}
