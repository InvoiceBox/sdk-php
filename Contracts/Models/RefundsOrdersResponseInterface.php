<?php
namespace Invoicebox\Contracts\Models;

interface RefundsOrdersResponseInterface{

    public function __construct(array $properties=[]);

    public function setOrders(array $orders);
    public function getOrders() : array ;
    public function getTotalCount() : int;
    public function getPageSize() : int;
    public function getPage() : int;
}