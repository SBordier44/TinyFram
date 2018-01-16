<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostIndexAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var PostTable
     */
    private $postTable;
    /**
     * @var CategoryTable
     */
    private $categoryTable;
    
    use RouterAwareAction;
    
    /**
     * PostIndexAction constructor.
     * @param RendererInterface $renderer
     * @param PostTable         $postTable
     * @param CategoryTable     $categoryTable
     */
    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        CategoryTable $categoryTable
    ) {
        $this->renderer      = $renderer;
        $this->postTable     = $postTable;
        $this->categoryTable = $categoryTable;
    }
    
    /**
     * @param Request $request
     * @return string
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     */
    public function __invoke(Request $request)
    {
        $params     = $request->getQueryParams();
        $posts      = $this->postTable->findPublic()->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        return $this->renderer->render('@blog/index', compact('posts', 'categories'));
    }
}
