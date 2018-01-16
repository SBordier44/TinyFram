<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{
    /**
     * @var string
     */
    protected $viewPath = '@blog/admin/categories';
    /**
     * @var string
     */
    protected $routePrefix = 'blog.category.admin';
    /**
     * @var array
     */
    protected $acceptedParams = ['name', 'slug'];
    
    /**
     * CategoryCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router            $router
     * @param CategoryTable     $table
     * @param FlashService      $flash
     */
    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
                     ->required('name', 'slug')
                     ->length('name', 2, 250)
                     ->length('slug', 2, 50)
                     ->unique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
                     ->slug('slug');
    }
}
