<?php
namespace Invoicebox\V2\Models;

use Invoicebox\Contracts\Models\RefundOrderResponseInterface;
use Invoicebox\Exceptions\NotExistPropertyException;

class RefundOrderResponse implements RefundOrderResponseInterface {
    private $resultCode;
    private $resultMessage;

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

    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    public function getResultMessage(): string
    {
        return $this->resultMessage;
    }

    public function isSuccess(): bool
    {
        if(is_null($this->resultCode) || !$this->resultCode == 0) return false;
        return !$this->resultCode;
    }
}