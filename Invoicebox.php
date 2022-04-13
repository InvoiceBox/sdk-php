<?php
namespace Invoicebox;

class Invoicebox
{
    const SDK_VERSION = "1.0";
    private $api_version;
    private $orders;
    /**
     * @var \Invoicebox\V3\Api | \Invoicebox\V2\Api
     */
    private $api;

    /**
     * Invoicebox constructor.
     * @param int $api_version
     * @param array $accessConfig
     * @param bool $testEnvExists
     * @param bool $isTestRequest
     * @throws \Invoicebox\Exceptions\ApiNotConfiguredException
     * @throws \Invoicebox\Exceptions\InvalidRequestException
     * @throws \Invoicebox\Exceptions\NotFoundException
     * @throws \Invoicebox\Exceptions\OperationErrorException
     */
    public function __construct(int $api_version, array $accessConfig, bool $testEnvExists=false, bool $isTestRequest=false)
    {
        if (2 === intval($api_version)) {
            $this->api_version = 2;
            $this->api = \Invoicebox\V2\Api::class;
        } else {
            $this->api_version = 3;
            $this->api = \Invoicebox\V3\Api::class;
        }

        $this->api::configure($accessConfig, $testEnvExists, $isTestRequest);
        if($this->api::isConfigured()){
            $this->orders = $this->getInstance('Orders', $accessConfig["key"]);
        }
        else throw new \Invoicebox\Exceptions\ApiNotConfiguredException();
    }
    
    /**
     * @return \Invoicebox\Contracts\OrdersInterface
     */
    public function Order()
    {
        return $this->orders;
    }
    
    /**
     * @return \Invoicebox\Contracts\Models\CartItemInterface
     */
    public function createCartItem()
    {
        return $this->getInstance('Models\CartItem');
    }
    
    /**
     * @return \Invoicebox\Contracts\Models\OrderInterface
     */
    public function createOrderData()
    {
        return $this->getInstance('Models\OrderData');
    }
    
    /**
     * @param $route
     * @param $params
     *
     * @return object|void
     */
    public function getInstance($route, $params = null)
    {
        $v2 = '\Invoicebox\V2\\';
        $v3 = '\Invoicebox\V3\\';
        $name = $route;
        
        switch ($this->api_version) {
            case 2:
                $name = $v2 . $route;
                break;
            case 3:
                $name = $v3 . $route;
                break;
        }

        return new $name($params);
    }
    
}