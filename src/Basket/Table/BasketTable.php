<?php

namespace App\Basket\Table;

use App\Basket\Entity\Basket;
use App\Basket\Entity\BasketRow;
use App\Basket\Helper\BasketHelper;
use App\Shop\Entity\Product;
use App\Shop\Table\ProductTable;
use Framework\Database\Hydrator;
use Framework\Database\Table;
use function array_map;
use function implode;

class BasketTable extends Table
{
    /**
     * @var string
     */
    protected $table = 'baskets';
    /**
     * @var string
     */
    protected $entity = Basket::class;
    /**
     * @var BasketRowTable
     */
    private $basketRowTable;
    /**
     * @var ProductTable
     */
    private $productTable;
    
    /**
     * BasketTable constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->productTable   = new ProductTable($pdo);
        $this->basketRowTable = new BasketRowTable($pdo);
        parent::__construct($pdo);
    }
    
    /**
     * @param BasketHelper $basket
     */
    public function hydrateBasket(BasketHelper $basket): void
    {
        $rows = $basket->getRows();
        if (!empty($rows)) {
            $ids = array_map(function (BasketRow $row) {
                return $row->getProductId();
            }, $rows);
            /** @var Product[] $products */
            $products     = $this->productTable->makeQuery()->where('id IN (' . implode(',', $ids) . ')')->fetchAll();
            $productsById = [];
            foreach ($products as $product) {
                $productsById[$product->getId()] = $product;
            }
            foreach ($rows as $row) {
                $row->setProduct($productsById[$row->getProductId()]);
            }
        }
    }
    
    /**
     * @return ProductTable
     */
    public function getProductTable(): ProductTable
    {
        return $this->productTable;
    }
    
    /**
     * @param Basket  $basket
     * @param Product $product
     * @param int     $quantity
     * @return BasketRow
     */
    public function addRow(Basket $basket, Product $product, int $quantity = 1): BasketRow
    {
        $params = [
            'basket_id'  => $basket->getId(),
            'product_id' => $product->getId(),
            'quantity'   => $quantity
        ];
        $this->basketRowTable->insert($params);
        $params['id'] = $this->getPdo()->lastInsertId();
        /** @var BasketRow $row */
        $row = Hydrator::hydrate($params, $this->basketRowTable->getEntity());
        $row->setProduct($product);
        return $row;
    }
    
    /**
     * @param int $userId
     * @return Basket
     */
    public function createForUser(int $userId): Basket
    {
        $params = [
            'user_id' => $userId
        ];
        $this->insert($params);
        $params['id'] = $this->getPdo()->lastInsertId();
        return Hydrator::hydrate($params, $this->getEntity());
    }
    
    /**
     * @param BasketRow $row
     * @param int       $quantity
     * @return BasketRow
     */
    public function updateRowQuantity(BasketRow $row, int $quantity): BasketRow
    {
        $this->basketRowTable->update($row->getId(), ['quantity' => $quantity]);
        $row->setQuantity($quantity);
        return $row;
    }
    
    /**
     * @param BasketRow $row
     */
    public function deleteRow(BasketRow $row): void
    {
        $this->basketRowTable->delete($row->getId());
    }
    
    /**
     * @param int $userId
     * @return Basket|null
     */
    public function findForUser(int $userId): ?Basket
    {
        return $this->makeQuery()->where("user_id = $userId")->fetch() ? : null;
    }
    
    /**
     * @param Basket $basketEntity
     * @return array
     */
    public function findRows(Basket $basketEntity): array
    {
        return $this->basketRowTable->makeQuery()->where("basket_id = {$basketEntity->getId()}")->fetchAll()->toArray();
    }
    
    /**
     * @param Basket $basket
     * @return int
     */
    public function deleteRows(Basket $basket): int
    {
        return $this->getPdo()->exec('DELETE FROM baskets_products WHERE basket_id = ' . $basket->getId());
    }
}
