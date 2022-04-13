<?php
namespace Invoicebox\V3\Models;

use Invoicebox\Contracts\Models\RefundOrderResponseInterface;

class RefundOrderResponse implements RefundOrderResponseInterface {
    private $id;
    private $parentId;
    private $description;
    private $merchantOrderId;
    private $merchantId;
    private $amount;
    private $vatAmount;
    private $currencyId;
    private $basketItems;
    private $createdAt;
    private $status;

    public function __construct(array $properties=[])
    {
        foreach ($properties as $property => $val){
            $this->set($property, $val);
        }
    }

    public function get(string $propertyName){
        if(property_exists($this, $propertyName)) return $this->$propertyName;
        else throw new NotExistPropertyException($propertyName);
    }

    public function set(string $propertyName, $value){
        $this->$propertyName = $value;
    }

    public function getUuid(): string
    {
        return $this->id;
    }

    public function getParentId(): string
    {
        return $this->parentId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isSuccess(): bool
    {
        if($this->status && $this->status == "created") return true;
        return false;
    }

}