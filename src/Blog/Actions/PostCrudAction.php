<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{
    /**
     * @var string
     */
    protected $viewPath = '@blog/admin/posts';
    /**
     * @var string
     */
    protected $routePrefix = 'blog.admin';
    /**
     * @var CategoryTable
     */
    private $categoryTable;
    /**
     * @var PostUpload
     */
    private $postUpload;
    
    /**
     * PostCrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router            $router
     * @param PostTable         $table
     * @param FlashService      $flash
     * @param CategoryTable     $categoryTable
     * @param PostUpload        $postUpload
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $router, $table, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload    = $postUpload;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Framework\Database\NoRecordException
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Post $post */
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->getImage());
        return parent::delete($request);
    }
    
    /**
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }
    
    /**
     * @return Post
     */
    protected function getNewEntity(): Post
    {
        $post = new Post();
        $post->setCreatedAt(new DateTime());
        return $post;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param mixed                  $item
     * @return array
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @internal param Post $post
     */
    protected function prePersist(ServerRequestInterface $request, $item): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        $image  = $this->postUpload->upload($params['image'], $item->getImage());
        if ($image) {
            $params['image'] = $image;
        } else {
            unset($params['image']);
        }
        $params = array_filter($params, function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id', 'image', 'published'], true);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return \Framework\Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        $validator = parent::getValidator($request)
                           ->required('content', 'name', 'slug', 'created_at', 'category_id')
                           ->length('content', 10)
                           ->length('name', 2, 250)
                           ->length('slug', 2, 50)
                           ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
                           ->dateTime('created_at')
                           ->extension('image', ['jpg', 'png'])
                           ->slug('slug');
        if (null === $request->getAttribute('id')) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
