<?php

namespace App\Basket\Action;

use App\Basket\Entity\Order;
use App\Basket\Table\OrderTable;
use Framework\Auth\AuthInterface;
use Framework\Exception\ForbiddenException;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class OrderInvoiceAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var AuthInterface
     */
    private $auth;
    /**
     * @var OrderTable
     */
    private $orderTable;
    
    /**
     * OrderInvoiceAction constructor.
     * @param RendererInterface $renderer
     * @param OrderTable        $orderTable
     * @param AuthInterface     $auth
     */
    public function __construct(RendererInterface $renderer, OrderTable $orderTable, AuthInterface $auth)
    {
        $this->renderer   = $renderer;
        $this->auth       = $auth;
        $this->orderTable = $orderTable;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \Framework\Exception\ForbiddenException
     * @throws \Framework\Database\NoRecordException
     */
    public function __invoke(ServerRequestInterface $request)
    {
        /** @var Order $order */
        $order = $this->orderTable->find($request->getAttribute('id'));
        $this->orderTable->findRows([$order]);
        $user = $this->auth->getUser();
        if (!$user || $user->getId() !== $order->getUserId()) {
            throw new ForbiddenException('Vous ne pouvez pas télécharger cette facture');
        }
        return $this->renderer->render('@basket/invoice', compact('order', 'user'));
    }
}
