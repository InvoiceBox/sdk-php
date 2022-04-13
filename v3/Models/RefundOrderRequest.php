<?php
namespace Invoicebox\V3\Models;

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Contracts\Models\RefundOrderRequestInterface;
use Invoicebox\Exceptions\NotValidRefundOrderData;

class RefundOrderRequest implements RefundOrderRequestInterface{
    private $parentId;
    private $merchantOrderId;
    private $amount;
    private $vatAmount;
    private $basketItems;
    private $description;
    private $type;

    public function __construct(string $type=null, string $orderId=null, string $merchantOrderId=null, string $refundId=null, float $amount=null, float $vatAmount=null, array $basketItems=null, string $description=null)
    {
        if(!is_null($orderId)) $this->setOrderId($orderId);
        if(!is_null($description)) $this->setDescription($description);
        if(!is_null($merchantOrderId)) $this->setMerchantId($merchantOrderId);
        if(!is_null($amount)) $this->setAmount($amount);
        if(!is_null($vatAmount)) $this->setVatAmount($vatAmount);
        if(!is_null($basketItems)) $this->setBasket($basketItems);
        if(!is_null($type)) $this->setType($type);
    }

    public function setOrderId(string $id){
        if(empty($id)) throw new NotValidRefundOrderData("Id заказа является обязательным полем");
        $this->parentId = $id;
    }

    public function setType(string $type)
    {
        switch ($type){
            case self::FULL_REFUND: $this->type = self::FULL_REFUND; break;
            case self::PARTIAL_REFUND: $this->type = self::PARTIAL_REFUND; break;
            case self::PENALTY_REFUND: $this->type = self::PENALTY_REFUND; break;
            default: throw new NotValidRefundOrderData("Тип возврата задан неправильно");
        }
    }

    public function setRefundId(string $id){
        // doesn't used in v3
    }

    public function setMerchantId(string $id){
        if(empty($id)) throw new NotValidRefundOrderData("Id заказа (в магазине) является обязательным полем");
        $this->merchantOrderId = $id;
    }
    public function setAmount(float $amount){
        $this->amount = floatval($amount);
    }
    public function setVatAmount(float $amount){
        $this->vatAmount = floatval($amount);
    }

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket)
    {
        $this->basketItems  = [];
        foreach ($basket as $row){
            if(!($row instanceof CartItemInterface)) new NotValidRefundOrderData("Элемент корзины должен быть экземпляром CartItemInterface");
            $this->basketItems[] = $row->formData();
        }
    }


    public function setBasketRow(array $basket)
    {
        $this->basketItems  = $basket;
    }


    public function setDescription(string $description)
    {
        if(empty($description)) throw new NotValidRefundOrderData("Описание является обязательным полем");
        if(strlen($description) > 1000) throw new NotValidRefundOrderData("Описание не должно быть длиннее 1000 знаков");
        $this->description = $description;
    }

    public function formData() :array {
        $data = [];
        $required_fields = [
            "parentId",
            "description",
            "merchantOrderId",
            "amount",
            "vatAmount",
            "basketItems",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        //if(empty($this->basketItems)) $not_filled[] = "basketItems";

        if(!empty($not_filled)) throw new NotValidRefundOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        return $data;
    }


}