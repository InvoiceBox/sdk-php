<?php

namespace Invoicebox\Contracts;


use Invoicebox\Contracts\Models\OrderInterface;
use Invoicebox\Contracts\Models\OrderResponseInterface;
use Invoicebox\Contracts\Models\OrdersResponseInterface;
use Invoicebox\Contracts\Models\RefundOrderRequestInterface;
use Invoicebox\Contracts\Models\RefundOrderResponseInterface;
use Invoicebox\Contracts\Models\RefundsOrdersResponseInterface;
use Invoicebox\Contracts\Models\UpdateOrderRequestInterface;

interface OrdersInterface{
    public function __construct(string $key);
    /**
     * @param OrderInterface $orderData
     * @throws \Exception
     * @return OrderResponseInterface | array
     */
    public function createOrder(OrderInterface $orderData);

    /**
     * @param string $id
     * @return OrderResponseInterface
     */
    public function getOrder(string $id) : OrderResponseInterface;

    /**
     * @param array[] $args ["key"=> $val]]
     * @return OrdersResponseInterface
     * @throws \Exception
     */
    public function getOrders(array $args=[]): OrdersResponseInterface;

    public function changeOrder(string $uuid, UpdateOrderRequestInterface $orderData) : OrderResponseInterface;

    /**
     * @param string $uuid
     * @param string|null $refundId
     * @return OrderResponseInterface|RefundOrderResponseInterface
     */
    public function cancelOrder(string $uuid, string $refundId=null);

    public function refundOrder(RefundOrderRequestInterface $request) : RefundOrderResponseInterface;

    public function getRefunds(array $args=[]): RefundsOrdersResponseInterface;
    
}