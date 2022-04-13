<?php
namespace Invoicebox\V3\Models;

use Invoicebox\Contracts\Models\OrderResponseInterface;
use Invoicebox\Exceptions\NotExistPropertyException;

class OrderResponse implements OrderResponseInterface {

    private $id;
    private $orderContainerId;
    private $paymentUrl;
    private $createdAt;
    private $status;
    private $paidAt;
    private $merchantId;
    private $description;
    private $merchantOrderId;
    private $amount;
    private $vatAmount;
    private $currencyId;
    private $languageId;
    private $expirationDate;
    private $basketItems;
    private $metaData;
    private $customer;
    private $notificationUrl;
    private $successUrl;
    private $failUrl;
    private $returnUrl;
    private $invoiceSetting;

    public function __construct(array $properties=[])
    {
        foreach ($properties as $property => $val){
            $this->set($property, $val);
        }
    }

    public function get($propertyName){
        if(property_exists($this, $propertyName)) return $this->$propertyName;
        else throw new NotExistPropertyException($propertyName);
    }

    public function set($propertyName, $value){
        $this->$propertyName = $value;
    }

    public function getUuid(): string
    {
        return $this->id;
    }

    public function getParentId(): string {
        return $this->orderContainerId;
    }

    public function getPaymentLink(): string
    {
        return $this->paymentUrl;
    }
}