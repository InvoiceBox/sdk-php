<?php
namespace Invoicebox\Contracts\Models;

interface OrderResponseInterface{

    public function __construct(array $properties=[]);

    public function getUuid() : string;
    public function getPaymentLink() : string;
}