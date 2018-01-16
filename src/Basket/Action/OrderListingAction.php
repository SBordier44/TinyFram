<?php

namespace App\Basket\Action;

use App\Basket\Table\OrderTable;
use Framework\Auth\AuthInterface;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use function compact;

class OrderListingAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var OrderTable
     */
    private $orderTable;
    /**
     * @var AuthInterface
     */
    private $auth;
    
    /**
     * OrderListingAction constructor.
     * @param RendererInterface $renderer
     * @param OrderTable        $orderTable
     * @param AuthInterface     $auth
     */
    public function __construct(
        RendererInterface $renderer,
        OrderTable $orderTable,
        AuthInterface $auth
    ) {
        $this->renderer   = $renderer;
        $this->orderTable = $orderTable;
        $this->auth       = $auth;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \Pagerfanta\Exception\OutOfRangeCurrentPageException
     * @throws \Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @throws \Pagerfanta\Exception\NotIntegerCurrentPageException
     * @throws \Pagerfanta\Exception\LessThan1MaxPerPageException
     * @throws \Pagerfanta\Exception\LessThan1CurrentPageException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $page   = $request->getQueryParams()['p'] ?? 1;
        $orders = $this->orderTable->findForUser($this->auth->getUser())->paginate(10, $page);
        if ($orders->count() > 0) {
            $this->orderTable->findRows($orders);
        }
        return $this->renderer->render('@basket/orders', compact('orders', 'page'));
    }
}
