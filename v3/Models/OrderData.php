<?php

namespace Invoicebox\V3\Models;

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Contracts\Models\CustomerInterface;
use Invoicebox\Contracts\Models\OrderInterface;
use Invoicebox\Exceptions\NotValidOrderData;

class OrderData implements OrderInterface{

    private $merchantId;
    private $description;
    private $merchantOrderId;
    private $amount;
    private $vatAmount;
    private $currencyId;
    private $languageId;
    private $expirationDate;
    /**
     * @var CartItemInterface
     */
    private $basketItems;
    private $metaData;
    /**
     * @var CustomerInterface
     */
    private $customer;
    private $notificationUrl;
    private $successUrl;
    private $failUrl;
    private $returnUrl;
    private $invoiceSetting;

    /**
     * OrderData constructor.
     * @param string|null $merchantId
     * @param string|null $description
     * @param int|null $merchantOrderId
     * @param float|null $amount
     * @param float|null $vatAmount
     * @param string|null $currencyId
     * @param string|null $languageId
     * @param \DateTime|null $expirationDate
     * @param CartItemInterface[] $basketItems
     * @param array|null $metaData
     * @param CustomerInterface|null $customer
     * @param string|null $notificationUrl
     * @param string|null $successUrl
     * @param string|null $failUrl
     * @param string|null $returnUrl
     * @param bool $customerLocked
     * @param array $customerLockedFields
     * @throws NotValidOrderData
     */
    public function __construct(string $merchantId=null, string $merchantCode=null, string $description=null, int $merchantOrderId=null,
                                float $amount=null, float $vatAmount=null, string $currencyId=null, string $languageId=null,
                                \DateTime $expirationDate=null, array $basketItems=[], array $metaData=null, CustomerInterface $customer=null,
                                string $notificationUrl=null, string $successUrl=null, string $failUrl=null, string $returnUrl=null,
                                bool $customerLocked=false, array $customerLockedFields=[])
    {
        if(!is_null($merchantId)) $this->setMerchantId($merchantId);
        if(!is_null($description)) $this->setDescription($description);
        if(!is_null($merchantOrderId)) $this->setOrderId($merchantOrderId);
        if(!is_null($amount)) $this->setTotalAmount($amount);
        if(!is_null($vatAmount)) $this->setTotalVatAmount($vatAmount);
        if(!is_null($currencyId)) $this->setCurrency($currencyId);
        if(!is_null($languageId)) $this->setLanguage($languageId);
        if(!is_null($expirationDate)) $this->setExpirationDate($expirationDate);
        if(!is_null($basketItems)) $this->setBasket($basketItems);
        if(!is_null($metaData)) $this->setMetadata($metaData);
        if(!is_null($customer)) $this->setCustomerData($customer);
        $this->setUrls([
            "notificationUrl" => $notificationUrl,
            "successUrl" => $successUrl,
            "failUrl" => $failUrl,
            "returnUrl" => $returnUrl,
        ]);
        $this->setInvoiceSettings($customerLocked, $customerLockedFields);
    }

    public function setMerchantId(string $id)
    {
        if(empty($id)) throw new NotValidOrderData("Id магазина является обязательным полем");
        if(strlen($id) > 36) throw new NotValidOrderData("Id магазина не может быть длиннее 36 знаков");
        $this->merchantId = $id;
    }

    public function setDescription(string $description)
    {
        if(empty($description)) throw new NotValidOrderData("Описание является обязательным полем");
        if(strlen($description) > 1000) throw new NotValidOrderData("Описание не должно быть длиннее 1000 знаков");
        $this->description = $description;
    }

    public function setOrderId(string $id)
    {
        if(empty($id)) throw new NotValidOrderData("Id заказа является обязательным полем");
        $this->merchantOrderId = $id;
    }

    public function setTotalAmount(float $amount)
    {
        $this->amount = floatval($amount);
    }

    public function setTotalVatAmount(float $vatAmount)
    {
        $this->vatAmount = floatval($vatAmount);
    }

    public function setCurrency(string $currency)
    {
        if(empty($currency))  throw new NotValidOrderData("Код валюты является обязательным полем");
        if(!in_array(mb_strtoupper($currency), $this->get_currencies()))  throw new NotValidOrderData("Код валюты неправильный");
        $this->currencyId = $currency;
    }

    public function setLanguage(string $language)
    {
        if(empty($language)) return;
        if(strlen($language) !== 2) throw new NotValidOrderData("Длина кода языка должна составлять 2 знака");
        if(!preg_match("/[a-z]{2}/", mb_strtolower($language))) throw new NotValidOrderData("Код языка имеет неправильный формат");

        $this->languageId = $language;
    }

    public function setExpirationDate(\DateTime $date)
    {
        if(empty($date)) throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        if (!($date instanceof \DateTime)) {
            throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        }
        $formatted = $date->format("Y-m-d") . 'T' . $date->format("H:i:sP");
        $this->expirationDate = $formatted;
    }

    public function setMetadata(array $metadata)
    {
        if(empty($metadata)) return;
        if(!is_array($metadata)) new NotValidOrderData("Метаданные заказа имеют неправильный формат");
        $this->metaData = $metadata;
    }

    /**
     * @param $urls string[]
     */
    public function setUrls(array $urls)
    {
        if(is_array($urls)){
            foreach (["notificationUrl", "successUrl", "failUrl", "returnUrl"] as $var){
                if(isset($urls[$var]) && !empty($urls[$var])){
                    if(!is_string($urls[$var])) new NotValidOrderData($var . " должен быть строкой");
                    if(strlen($urls[$var]) > 1000) new NotValidOrderData("Длина " . $var . " не может быть больше 1000 знаков");
                    $this->$var = strval($urls[$var]);
                }
            }
        }
    }

    public function setTest(bool $test)
    {
        // doesn't used in v3
    }

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket)
    {
        $this->basketItems  = [];
        foreach ($basket as $row){
            if(!($row instanceof CartItemInterface)) new NotValidOrderData("Элемент корзины должен быть экземпляром CartItemInterface");
            $this->basketItems[] = $row;
        }
    }

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomerData(CustomerInterface $customer)
    {
        $this->customer = $customer;
    }

    public function setInvoiceSettings(bool $customerLocked=false, array $customerLockedFields=[]){
        if($customerLocked || $customerLockedFields) $this->invoiceSetting = [];
        $this->invoiceSetting["customerLocked"] = $customerLocked;
        if(!empty($customerLockedFields)) $this->invoiceSetting["customerLockedFields"] = $customerLockedFields;
    }

    /**
     * @return array
     * @throws NotValidOrderData
     */
    public function formData(): array
    {
        $data = [];
        $required_fields = [
            "merchantId",
            "description",
            "merchantOrderId",
            "amount",
            "vatAmount",
            "currencyId",
            "expirationDate",
            ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($this->basketItems)) {
            $data["basketItems"] = [];
            foreach ($this->basketItems as $item){
                $data["basketItems"][] = $item->formData();
            }
            if(empty($data["basketItems"])) $not_filled[] = "basketItems";
        }
        else $not_filled[] = "basketItems";

        if(!empty($this->customer)) $data["customer"] = $this->customer->formData();
        else $not_filled[] = "customer";

        if(!empty($not_filled)) throw new NotValidOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        $not_required = [
            "languageId",
            "metaData",
            "notificationUrl",
            "successUrl",
            "failUrl",
            "returnUrl",
            "invoiceSetting",

        ];

        foreach ($not_required as $field) {
            if(!is_null($this->$field)) $data[$field] = $this->$field;
        }

        return $data;
    }

    public function get_currencies(){
        return [
            "RUB",
            "EUR",
            "GBP",
            "USD"
        ];
    }

    public function setMerchantCode(string $code)
    {
    }
}