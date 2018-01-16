<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Psr\Http\Message\{
	ResponseInterface, ServerRequestInterface
};

class PostShowAction
{
	/**
	 * @var RendererInterface
	 */
	private $renderer;
	/**
	 * @var Router
	 */
	private $router;
	/**
	 * @var PostTable
	 */
	private $postTable;
	
	use RouterAwareAction;
	
	/**
	 * PostShowAction constructor.
	 * @param RendererInterface $renderer
	 * @param Router            $router
	 * @param PostTable         $postTable
	 */
	public function __construct(
		RendererInterface $renderer,
		Router $router,
		PostTable $postTable
	) {
		$this->renderer  = $renderer;
		$this->router    = $router;
		$this->postTable = $postTable;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface|string
	 */
	public function __invoke(ServerRequestInterface $request)
	{
		$slug = $request->getAttribute('slug');
		$post = $this->postTable->findWithCategory($request->getAttribute('id'));
		if ($post->getSlug() !== $slug) {
			return $this->redirect('blog.show', [
				'slug' => $post->getSlug(),
				'id'   => $post->getId()
			]);
		}
		return $this->renderer->render('@blog/show', [
			'post' => $post
		]);
	}
}
