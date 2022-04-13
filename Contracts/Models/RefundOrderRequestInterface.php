<?php
namespace Invoicebox\Contracts\Models;


interface RefundOrderRequestInterface
{

    const FULL_REFUND = "full";
    const PARTIAL_REFUND = "partial";
    const PENALTY_REFUND = "penalty";

    public function __construct(string $type=null, string $orderId=null, string $merchantOrderId=null, string $refundId=null, float $amount=null, float $vatAmount=null, array $basketItems=null, string $description=null);

    public function setType(string $type);

    public function setOrderId(string $id);

    public function setRefundId(string $id);

    public function setMerchantId(string $id);

    public function setAmount(float $amount);

    public function setVatAmount(float $amount);

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket);


    public function setBasketRow(array $basket);


    public function setDescription(string $description);

    public function formData() :array;
}