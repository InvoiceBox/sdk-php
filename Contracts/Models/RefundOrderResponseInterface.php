<?php
namespace Invoicebox\Contracts\Models;


interface RefundOrderResponseInterface
{
    public function __construct(array $properties=[]);

    public function get(string $propertyName);

    public function set(string $propertyName, $value);

    public function isSuccess(): bool;
}