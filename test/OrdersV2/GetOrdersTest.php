<?php

use PHPUnit\Framework\TestCase;

class GetOrdersTestV2 extends TestCase
{
    private $IB;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $configFile = $configFile = __DIR__ . "/../configv2.json";
        $config = json_decode(file_get_contents($configFile),true);
        $this->IB = new \Invoicebox\Invoicebox(2, $config, false, true);
    }
    
    public function testNotFoundOrder()
    {
        $this->expectException(\Invoicebox\Exceptions\OperationErrorException::class);
        $result = $this->IB->Order()->getOrder(0);
    }

    public function testGetOrder(){
        $result = $this->IB->Order()->getOrder("78043-37920-89015-18373");
        $this->assertInstanceOf(\Invoicebox\Contracts\Models\OrderResponseInterface::class, $result );
    }
}