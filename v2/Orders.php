<?php

namespace Invoicebox\V2;

use Invoicebox\Contracts\Models\OrderInterface;
use Invoicebox\Contracts\Models\OrderResponseInterface;
use Invoicebox\Contracts\Models\OrdersResponseInterface;
use Invoicebox\Contracts\Models\RefundOrderRequestInterface;
use Invoicebox\Contracts\Models\RefundOrderResponseInterface;
use Invoicebox\Contracts\Models\RefundsOrdersResponseInterface;
use Invoicebox\Contracts\Models\UpdateOrderRequestInterface;
use Invoicebox\Contracts\OrdersInterface;
use Invoicebox\Exceptions\InvalidRequestException;
use Invoicebox\V2\Models\RefundOrderResponse;

final class Orders implements OrdersInterface
{

    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param OrderInterface $orderData
     * @throws \Exception
     * @return array
     */
    public function createOrder(OrderInterface $orderData): array
    {
        $hash = $orderData->get("itransfer_participant_id") . $orderData->get("itransfer_order_id") . $orderData->get("itransfer_order_amount") . $orderData->get("itransfer_order_currency_ident");
        $data = $orderData->formData();
        $data["itransfer_participant_sign"] = $this->generateSign($hash);

        return $data;
    }

    /**
     * @param string $id
     * @return OrderResponseInterface
     * @throws InvalidRequestException
     * @throws \Invoicebox\Exceptions\ApiNotConfiguredException
     */
    public function getOrder(string $id): OrderResponseInterface
    {
        $result = Api::post('', ["function" => "getInvoiceDetails", "ucode" => $id, "sign" => $this->generateSign($id)]);
        if(is_array($result) && isset($result["data"])) {
            return new OrderResponse(array_shift($result["data"]));
        }
        throw new InvalidRequestException();
    }

    /**
     * @param array[] $args ["key"=> $val]]
     * @return OrdersResponseInterface
     * @throws \Exception
     */
    public function getOrders(array $args = []): OrdersResponseInterface
    {
        throw new InvalidRequestException();
    }

    public function changeOrder(string $uuid, UpdateOrderRequestInterface $orderData): OrderResponseInterface
    {
        throw new InvalidRequestException();
    }

    /**
     * Этот метод используется для полной отмены заказа. Если заказ был оплачен, средства полностью возвратятся клиенту.
     * В переданном объекте RefundOrderRequest должен быть установлен тип RefundOrderRequestInterface::FULL_REFUND
     * @param string $uuid
     * @param string|null $refundId
     * @return RefundOrderResponseInterface
     * @throws \Invoicebox\Exceptions\ApiNotConfiguredException
     */
    public function cancelOrder(string $uuid, string $refundId=null)
    {
        $hash = $uuid . $refundId;
        $data = ["function" => "disableInvoice", "ucode" => $uuid, "ident" => $refundId];
        $data["sign"] = $this->generateSign($hash);
        $result = Api::post('', $data);
        //{"resultCode":"0","resultMessage":"Операция выполнена без ошибок"}
        if(is_array($result) && isset($result["resultCode"])) {
            return new RefundOrderResponse($result);
        }
        throw new \Exception();
    }

    /**
     * Этот метод используется для частичного возврата по заказу.
     * Чтобы вернуть указанную сумму, в переданном объекте RefundOrderRequest должен быть установлен тип RefundOrderRequestInterface::PARTIAL_REFUND и заполнено поле basket
     * Чтобы вернуть полную сумму за вычетом штрафа, в переданном объекте RefundOrderRequest должен быть установлен тип RefundOrderRequestInterface::PENALTY_REFUND
     * @param RefundOrderRequestInterface $request
     * @return RefundOrderResponseInterface
     * @throws InvalidRequestException
     * @throws \Invoicebox\Exceptions\ApiNotConfiguredException
     */
    public function refundOrder(RefundOrderRequestInterface $request): RefundOrderResponseInterface
    {
        $hash = $request->get("ucode") . $request->get("ident") . $request->get("comment");
        $data = $request->formData();
        if($request->get("type") === RefundOrderRequestInterface::PARTIAL_REFUND) {
            $hash .= $request->get("amount");
            $data["sign"] = $this->generateSign($hash);
            $data["function"] = "disableInvoiceExA";
            $result = Api::post('', $data);
        }
        elseif($request->get("type") === RefundOrderRequestInterface::PENALTY_REFUND) {
            $hash .= $request->get("penalty");
            $data["sign"] = $this->generateSign($hash);
            $data["function"] = "disableInvoiceExB";
            $result = Api::post('',$data);
        }
        else{
            throw new InvalidRequestException("Тип возврата задан неправильно");
        }
        if(is_array($result) && isset($result["resultCode"])) {
            return new RefundOrderResponse($result);
        }
        throw new InvalidRequestException();
    }

    public function getRefunds(array $args = []): RefundsOrdersResponseInterface
    {
        throw new InvalidRequestException();
    }

    private function generateSign($string){
        return md5( $string . $this->key);
    }
}
