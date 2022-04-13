<?php

namespace Invoicebox\V3;

use Invoicebox\Contracts\Models\OrderInterface;
use Invoicebox\Contracts\Models\OrderResponseInterface;
use Invoicebox\Contracts\Models\OrdersResponseInterface;
use Invoicebox\Contracts\Models\RefundOrderRequestInterface;
use Invoicebox\Contracts\Models\RefundOrderResponseInterface;
use Invoicebox\Contracts\Models\RefundsOrdersResponseInterface;
use Invoicebox\Contracts\Models\UpdateOrderRequestInterface;
use Invoicebox\Contracts\OrdersInterface;
use Invoicebox\V3\Models\OrderResponse;
use Invoicebox\V3\Models\OrdersResponse;
use Invoicebox\V3\Models\RefundOrderResponse;
use Invoicebox\V3\Models\RefundsOrdersResponse;

final class Orders implements OrdersInterface
{
    public function __construct(string $key){

    }
    /**
     * @param OrderInterface $orderData
     * @throws \Exception
     * @return OrderResponseInterface
     */
    public function createOrder(OrderInterface $orderData) : OrderResponseInterface
    {
        $data = $orderData->formData();
        if($data) {
            $result = Api::post('/v3/billing/api/order/order', $data);
            if(is_array($result) && isset($result["data"])){
                $resultData = $result["data"];
                if(isset($resultData["id"]) && isset($resultData["paymentUrl"])){
                    $response = new OrderResponse($resultData);
                    return $response;
                }

            }
        }
        throw new \Exception();
    }

    public function getOrder(string $id) : OrderResponseInterface
    {
        $result = Api::get('/v3/filter/api/order/order', ["id" => $id]);
        if(is_array($result) && isset($result["data"])) {
            return new OrderResponse(array_shift($result["data"]));
        }
        throw new \Exception();
    }

    /**
     * @param array[] $args ["key"=> $val]
     * @return OrdersResponseInterface
     * @throws \Exception
     */
    public function getOrders(array $args=[]) : OrdersResponseInterface {
        $url = '/v3/filter/api/order/order';
        $result = Api::get($url, $args);
        if(is_array($result)) {
            return new OrdersResponse($result);
        }
        throw new \Exception();
    }

    public function changeOrder(string $uuid, UpdateOrderRequestInterface $orderData) : OrderResponseInterface
    {
        $data = $orderData->formData();
        if($data) {
            $result = Api::put('/v3/billing/api/order/order/' . $uuid, $data);
            $resultData = $result["data"];
            if(isset($resultData["id"]) && isset($resultData["paymentUrl"])){
                $response = new OrderResponse($resultData);
                return $response;
            }
        }
        throw new \Exception();
    }

    public function cancelOrder(string $uuid, string $refundId=null) : OrderResponseInterface
    {
        $result = Api::delete('/v3/billing/api/order/order/' . $uuid);
        $resultData = $result["data"];
        if(isset($resultData["id"]) && isset($resultData["paymentUrl"])){
            $response = new OrderResponse($resultData);
            return $response;
        }
        throw new \Exception();
    }

    public function getRefundOrderBasket($uuid){
        $result = Api::get('/v3/billing/api/order/order/' . $uuid .'/refund-basket-item');
        if(is_array($result) && isset($result["data"])) {
           return $result["data"];
        }
        throw new \Exception();
    }

    public function refundOrder(RefundOrderRequestInterface $request) : RefundOrderResponseInterface
    {
        $result = Api::post('/v3/billing/api/order/refund-order', $request->formData());
        if(is_array($result) && isset($result["data"])) {
            $response = new RefundOrderResponse($result["data"]);
            return $response;
        }
        throw new \Exception();

    }

    public function getRefunds(array $args=[]): RefundsOrdersResponseInterface
    {
        $url = '/v3/filter/api/order/refund-order';
        $result = Api::get($url, $args);
        if(is_array($result)) {
            return new RefundsOrdersResponse($result);
        }
        throw new \Exception();
    }
}