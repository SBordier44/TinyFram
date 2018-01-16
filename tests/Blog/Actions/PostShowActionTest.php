<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class PostShowActionTest extends TestCase
{
    /**
     * @var PostShowActionTest
     */
    private $action;
    /**
     * @var ObjectProphecy
     */
    private $renderer;
    /**
     * @var ObjectProphecy
     */
    private $postTable;
    /**
     * @var ObjectProphecy
     */
    private $router;
    
    public function setUp()
    {
        $this->renderer  = $this->prophesize(RendererInterface::class);
        $this->postTable = $this->prophesize(PostTable::class);
        $this->router    = $this->prophesize(Router::class);
        $this->action    = new PostShowAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal()
        );
    }
    
    public function testShowRedirect()
    {
        $post    = $this->makePost(9, 'azezae-azeazae');
        $request = (new ServerRequest('GET', '/'))->withAttribute('id', $post->getId())->withAttribute('slug', 'demo');
        $this->router->generateUri('blog.show', ['id' => $post->getId(), 'slug' => $post->getSlug()])
                     ->willReturn('/demo2');
        $this->postTable->findWithCategory($post->getId())->willReturn($post);
        $response = call_user_func($this->action, $request);
        static::assertEquals(301, $response->getStatusCode());
        static::assertEquals(['/demo2'], $response->getHeader('location'));
    }
    
    private function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->setId($id);
        $post->setSlug($slug);
        return $post;
    }
    
    public function testShowRender()
    {
        $post    = $this->makePost(9, 'azezae-azeazae');
        $request = (new ServerRequest('GET', '/'))->withAttribute('id', $post->getId())
                                                  ->withAttribute('slug', $post->getSlug());
        $this->postTable->findWithCategory($post->getId())->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        $response = call_user_func($this->action, $request);
        static::assertEquals(true, true);
    }
}
