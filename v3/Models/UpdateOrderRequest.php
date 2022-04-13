<?php
namespace Invoicebox\V3\Models;

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Contracts\Models\CustomerInterface;
use Invoicebox\Contracts\Models\UpdateOrderRequestInterface;
use Invoicebox\Exceptions\NotValidOrderData;

class UpdateOrderRequest implements UpdateOrderRequestInterface {
    private $amount;
    private $vatAmount;
    private $basketItems;
    private $description;
    private $expirationDate;
    private $metaData;
    private $customer;

    public function __construct(string $description=null, float $amount=null, float $vatAmount=null, \DateTime $expirationDate=null, array $basketItems=null, array $metaData=null, CustomerInterface $customer=null)
    {
        if(!is_null($description)) $this->setDescription($description);
        if(!is_null($amount)) $this->setAmount($amount);
        if(!is_null($vatAmount)) $this->setVatAmount($vatAmount);
        if(!is_null($expirationDate)) $this->setExpirationDate($expirationDate);
        if(!is_null($basketItems)) $this->setBasket($basketItems);
        if(!is_null($metaData)) $this->setMetadata($metaData);
        if(!is_null($customer)) $this->setCustomerData($customer);
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
            if(!($row instanceof CartItemInterface)) new NotValidOrderData("Элемент корзины должен быть экземпляром CartItemInterface");
            $this->basketItems[] = $row->formData();
        }
    }


    public function setBasketRow(array $basket)
    {
        $this->basketItems  = $basket;
    }


    public function setDescription(string $description)
    {
        if(empty($description)) throw new NotValidOrderData("Описание является обязательным полем");
        if(strlen($description) > 1000) throw new NotValidOrderData("Описание не должно быть длиннее 1000 знаков");
        $this->description = $description;
    }

    public function setExpirationDate(\DateTime $date)
    {
        if(empty($date)) throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        if (!($date instanceof \DateTime)) {
            throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        }
        $this->expirationDate = $date->format("yyyy-MM-dd'T'HH:mm:ssP");
    }

    public function setMetadata(array $metadata)
    {
        if(empty($metadata)) return;
        if(!is_array($metadata)) new NotValidOrderData("Метаданные заказа имеют неправильный формат");
        $this->metaData = $metadata;
    }

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomerData(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    public function formData() :array {
        $data = [];
        $not_required_fields = [
            "description",
            "amount",
            "vatAmount",
            "expirationDate",
            "basketItems",
            "metaData",
            "customer",
        ];


        foreach ($not_required_fields as $field) {
            if(!is_null($this->$field) && (! "basketItems" === $field || !empty($this->$field))) $data[$field] = $this->$field;
        }

        return $data;
    }


}