<?php
namespace Invoicebox\V2\Models;

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Contracts\Models\RefundOrderRequestInterface;
use Invoicebox\Exceptions\NotExistPropertyException;
use Invoicebox\Exceptions\NotValidRefundOrderData;

class RefundOrderRequest implements RefundOrderRequestInterface{
    private $ucode;
    private $ident;
    private $amount;
    private $vatAmount;
    private $basket;
    private $comment;
    private $type;
    private $refundId;
    private $penalty;

    public function __construct(string $type=null, string $orderId=null, string $merchantOrderId=null, string $refundId=null, float $amount=null, float $vatAmount=null, array $basketItems=null, string $description=null, float $penalty=null)
    {
        if(!is_null($orderId)) $this->setOrderId($orderId);
        if(!is_null($description)) $this->setDescription($description);
        if(!is_null($merchantOrderId)) $this->setMerchantId($merchantOrderId);
        if(!is_null($amount)) $this->setAmount($amount);
        if(!is_null($vatAmount)) $this->setVatAmount($vatAmount);
        if(!is_null($basketItems)) $this->setBasketRow($basketItems);
        if(!is_null($type)) $this->setType($type);
        if(!is_null($refundId)) $this->setRefundID($refundId);
        if(!is_null($penalty)) $this->setPenalty($penalty);
    }

    public function get($propertyName){
        if(property_exists($this, $propertyName)) return $this->$propertyName;
        else throw new NotExistPropertyException($propertyName);
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

    public function setOrderId(string $id){
        if(empty($id)) throw new NotValidRefundOrderData("Id заказа является обязательным полем");
        $this->ucode = $id;
    }
    public function setMerchantId(string $id){
        //if(empty($id)) throw new NotValidRefundOrderData("Id заказа (в магазине) является обязательным полем");
        //$this->ident = $id;
    }

    public function setRefundID(string $id){
        if(empty($id)) throw new NotValidRefundOrderData("Id возврата (в магазине) является обязательным полем");
        $this->ident = $id;
    }
    public function setAmount(float $amount){
        $this->amount = floatval($amount);
    }
    public function setVatAmount(float $amount){
        $this->vatAmount = floatval($amount);
    }

    public function setPenalty(float $penalty){
        $this->penalty = floatval($penalty);
    }

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket)
    {
        $this->basket  = [];
        foreach ($basket as $row){
            if(!($row instanceof CartItemInterface)) new NotValidRefundOrderData("Элемент корзины должен быть экземпляром CartItemInterface");
            $el = [];
            foreach ($row->formData() as $key => $value){
                $el[array_pop(explode("_", $key))] = $value;
            }
            $this->basket[] = $el;
        }
    }


    public function setBasketRow(array $basket)
    {
        $this->basket  = $basket;
    }


    public function setDescription(string $description)
    {
        if(empty($description)) throw new NotValidRefundOrderData("Описание является обязательным полем");
        if(strlen($description) > 1000) throw new NotValidRefundOrderData("Описание не должно быть длиннее 1000 знаков");
        $this->comment = $description;
    }

    public function formData() :array {
        if(empty($this->type)) throw new NotValidRefundOrderData("Тип возврата задан неправильно");
        if($this->type === self::PARTIAL_REFUND) return $this->formPartialData();
        if($this->type === self::PENALTY_REFUND) return $this->formDataWithPentalty();
        $data = [];
        $required_fields = [
            "ucode",
            "ident",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidRefundOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        return $data;
    }

    public function formPartialData() : array {
        $data = [];
        $required_fields = [
            "ucode",
            "ident",
            "basket",
            "comment",
            "amount",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidRefundOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        return $data;
    }

    public function formDataWithPentalty() :array{
        $data = [];
        $required_fields = [
            "ucode",
            "ident",
            "comment",
            "penalty",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidRefundOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        return $data;
    }


}