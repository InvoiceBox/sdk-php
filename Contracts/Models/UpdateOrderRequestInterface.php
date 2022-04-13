<?php
namespace Invoicebox\Contracts\Models;


interface UpdateOrderRequestInterface
{
    public function __construct(string $description=null, float $amount=null, float $vatAmount=null, \DateTime $expirationDate=null, array $basketItems=null, array $metaData=null, CustomerInterface $customer=null);

    public function setAmount(float $amount);

    public function setVatAmount(float $amount);

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket);


    public function setBasketRow(array $basket);


    public function setDescription(string $description);

    public function setExpirationDate(\DateTime $date);

    public function setMetadata(array $metadata);

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomerData(CustomerInterface $customer);

    public function formData() :array;

}