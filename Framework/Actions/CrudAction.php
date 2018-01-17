<?php

namespace Framework\Actions;

use Framework\Database\Hydrator;
use Framework\Database\Table;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CrudAction
{
    /**
     * @var Table
     */
    protected $table;
    /**
     * @var string
     */
    protected $viewPath;
    /**
     * @var string
     */
    protected $routePrefix;
    /**
     * @var array
     */
    protected $messages = [
        'create' => "L'élément a bien été créé",
        'edit'   => "L'élément a bien été modifié"
    ];
    /**
     * @var array
     */
    protected $acceptedParams = [];
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flash;
    
    use RouterAwareAction;
    
    /**
     * CrudAction constructor.
     * @param RendererInterface $renderer
     * @param Router            $router
     * @param Table             $table
     * @param FlashService      $flash
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        Table $table,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->router   = $router;
        $this->table    = $table;
        $this->flash    = $flash;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     * @throws \Framework\Database\NoRecordException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->table->delete($request->getAttribute('id'));
        return $this->redirect($this->routePrefix . '.index');
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->prePersist($request, $item));
                $this->postPersist($request, $item);
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }
            Hydrator::hydrate($request->getParsedBody(), $item);
            $errors = $validator->getErrors();
        }
        return $this->renderer->render($this->viewPath . '/create', $this->formParams(compact('item', 'errors')));
    }
    
    /**
     * @return mixed
     */
    protected function getNewEntity()
    {
        $entity = $this->table->getEntity();
        return new $entity();
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    protected function prePersist(ServerRequestInterface $request, $item): array
    {
        return array_filter(array_merge($request->getParsedBody(), $request->getUploadedFiles()), function ($key) {
            return \in_array($key, $this->acceptedParams, true);
        }, ARRAY_FILTER_USE_KEY);
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param mixed                  $item
     */
    protected function postPersist(ServerRequestInterface $request, $item): void
    {
    }
    
    /**
     * @param $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     * @throws \Framework\Database\NoRecordException
     */
    public function edit(ServerRequestInterface $request)
    {
        $id   = (int)$request->getAttribute('id');
        $item = $this->table->find($id);
        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($id, $this->prePersist($request, $item));
                $this->postPersist($request, $item);
                $this->flash->success($this->messages['edit']);
                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();
            Hydrator::hydrate($request->getParsedBody(), $item);
        }
        return $this->renderer->render($this->viewPath . '/edit', $this->formParams(compact('item', 'errors')));
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items  = $this->table->findAll()->paginate(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }
}
