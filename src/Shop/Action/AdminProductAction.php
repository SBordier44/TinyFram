<?php

namespace App\Shop\Action;

use App\Shop\Entity\Product;
use App\Shop\Table\ProductTable;
use App\Shop\Upload\ProductImageUpload;
use DateTime;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router\Router;
use Framework\Session\FlashService;
use Framework\Validator\Validator;
use InvalidArgumentException;
use Psr\Http\Message\{
	ResponseInterface, ServerRequestInterface
};
use RuntimeException;
use const ARRAY_FILTER_USE_KEY;
use function array_filter;
use function array_merge;

class AdminProductAction extends CrudAction
{
	/**
	 * @var string
	 */
	protected $viewPath = '@shop/admin/products';
	/**
	 * @var string
	 */
	protected $routePrefix = 'shop.admin.products';
	/**
	 * @var array
	 */
	protected $acceptedParams = ['name', 'slug', 'price', 'created_at', 'description'];
	/**
	 * @var ProductImageUpload
	 */
	private $imageUpload;
	
	/**
	 * AdminProductAction constructor.
	 * @param RendererInterface  $renderer
	 * @param Router             $router
	 * @param ProductTable       $table
	 * @param FlashService       $flash
	 * @param ProductImageUpload $imageUpload
	 */
	public function __construct(
		RendererInterface $renderer,
		Router $router,
		ProductTable $table,
		FlashService $flash,
		ProductImageUpload $imageUpload
	) {
		parent::__construct($renderer, $router, $table, $flash);
		$this->imageUpload = $imageUpload;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 * @throws \Framework\Database\NoRecordException
	 */
	public function delete(ServerRequestInterface $request): ResponseInterface
	{
		/** @var Product $product */
		$product = $this->table->find($request->getAttribute('id'));
		$this->imageUpload->delete($product->getImage());
		return parent::delete($request);
	}
	
	/**
	 * @return Product
	 */
	protected function getNewEntity(): Product
	{
		/**
		 * @var Product $entity
		 */
		$entity = parent::getNewEntity();
		$entity->setCreatedAt(new DateTime());
		return $entity;
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @param mixed                  $item
	 * @return array
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	protected function prePersist(ServerRequestInterface $request, $item): array
	{
		$params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
		$image  = $this->imageUpload->upload($params['image'], $item->getImage());
		if ($image) {
			$params['image']        = $image;
			$this->acceptedParams[] = 'image';
		}
		return array_filter($params, function ($key) {
			return \in_array($key, $this->acceptedParams, true);
		}, ARRAY_FILTER_USE_KEY);
	}
	
	/**
	 * @param ServerRequestInterface $request
	 * @return Validator
	 */
	protected function getValidator(ServerRequestInterface $request): Validator
	{
		$validator = parent::getValidator($request)
						   ->required($this->acceptedParams)
						   ->length('name', 5)
						   ->length('slug', 5)
						   ->slug('slug')
						   ->unique('slug', $this->table, null, $request->getAttribute('id'))
						   ->length('description', 5)
						   ->numeric('price')
						   ->dateTime('created_at')
						   ->extension('image', ['jpg', 'png', 'jpeg', 'gif']);
		if ($request->getAttribute('id') === null) {
			$validator->uploaded('image');
		}
		return $validator;
	}
}
