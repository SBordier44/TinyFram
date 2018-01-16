<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryShowAction
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
     * CategoryShowAction constructor.
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
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(Request $request)
    {
        $params     = $request->getQueryParams();
        $category   = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        $posts      = $this->postTable->findPublicForCategory($category->id)->paginate(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        $page       = $params['p'] ?? 1;
        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
