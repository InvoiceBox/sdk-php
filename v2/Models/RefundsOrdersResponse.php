<?php

namespace Invoicebox\V2\Models;
use Invoicebox\Contracts\Models\RefundsOrdersResponseInterface;

class RefundsOrdersResponse implements RefundsOrdersResponseInterface
{

    private $orders;
    private $totalCount;
    private $pageSize;
    private $page;
    private $extendedData;

    public function __construct(array $properties = [])
    {
        foreach ($properties as $property => $val){
            $this->set($property, $val);
        }
    }

    public function set($propertyName, $value){
        if($propertyName === "data") $this->setOrders($value);
        elseif ($propertyName === "metaData") $this->setMeta($value);
        else $this->$propertyName = $value;
    }

    public function setOrders(array $orders)
    {
        $data = [];
        foreach ($orders as $row){
            if(empty($row)) continue;
            $data[] =  new RefundOrderResponse($row);
        }
        $this->orders = $data;
    }

    public function setMeta(array $meta){
        foreach ($meta as $property => $val){
            $this->set($property, $val);
        }
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getExtendedData(): array {
        $this->extendedData;
    }
}