<?php

namespace Invoicebox\V2\Models;

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Contracts\Models\CustomerInterface;
use Invoicebox\Contracts\Models\OrderInterface;
use Invoicebox\Exceptions\NotExistPropertyException;
use Invoicebox\Exceptions\NotValidOrderData;

class OrderData implements OrderInterface{
    private $itransfer_language_ident;
    private $itransfer_participant_id;
    private $itransfer_participant_ident;
    private $itransfer_order_id;
    private $itransfer_order_quantity;
    private $itransfer_order_amount;
    private $itransfer_order_amount_vat;
    private $itransfer_order_currency_ident;
    private $itransfer_order_description;
    private $itransfer_body_type;
    private $itransfer_timelimit;
    private $itransfer_person_name;
    private $itransfer_person_email;
    private $itransfer_person_phone;
    private $itransfer_url_returnsuccess;
    private $itransfer_url_return;
    private $basketItems;

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
        if(!is_null($merchantCode)) $this->setMerchantCode($merchantCode);
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
            "itransfer_url_returnsuccess" => $successUrl,
            "itransfer_url_return" => $returnUrl,
        ]);
        $this->setInvoiceSettings($customerLocked, $customerLockedFields);
    }

    public function get($propertyName){
        if(property_exists($this, $propertyName)) return $this->$propertyName;
        else throw new NotExistPropertyException($propertyName);
    }

    public function setMerchantId(string $id)
    {
        if(empty($id)) throw new NotValidOrderData("Id магазина является обязательным полем");
        if(strlen($id) > 36) throw new NotValidOrderData("Id магазина не может быть длиннее 36 знаков");
        $this->itransfer_participant_id = $id;
    }

    public function setMerchantCode(string $code)
    {
        if(empty($code)) throw new NotValidOrderData("Код магазина является обязательным полем");
        if(strlen($code) > 36) throw new NotValidOrderData("Код магазина не может быть длиннее 36 знаков");
        $this->itransfer_participant_ident = $code;
    }

    public function setDescription(string $description)
    {
        if(empty($description)) throw new NotValidOrderData("Описание является обязательным полем");
        if(strlen($description) > 1000) throw new NotValidOrderData("Описание не должно быть длиннее 1000 знаков");
        $this->itransfer_order_description = $description;
    }

    public function setOrderId(string $id)
    {
        if(empty($id)) throw new NotValidOrderData("Id заказа является обязательным полем");
        $this->itransfer_order_id = $id;
    }

    public function setTotalAmount(float $amount)
    {
        $this->itransfer_order_amount = floatval($amount);
    }

    public function setTotalVatAmount(float $vatAmount)
    {
        $this->itransfer_order_amount_vat = floatval($vatAmount);
    }

    public function setCurrency(string $currency)
    {
        if(empty($currency))  throw new NotValidOrderData("Код валюты является обязательным полем");
        if(!in_array(mb_strtoupper($currency), $this->get_currencies()))  throw new NotValidOrderData("Код валюты неправильный");
        $this->itransfer_order_currency_ident = $currency;
    }

    public function setLanguage(string $language)
    {
        if(empty($language)) return;
        if(strlen($language) !== 2) throw new NotValidOrderData("Длина кода языка должна составлять 2 знака");
        if(!preg_match("/[a-z]{2}/", mb_strtolower($language))) throw new NotValidOrderData("Код языка имеет неправильный формат");

        $this->itransfer_language_ident = $language;
    }

    public function setExpirationDate(\DateTime $date)
    {
        if(empty($date)) throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        if (!($date instanceof \DateTime)) {
            throw new NotValidOrderData("Дата истечения заказа является обязательным полем");
        }
        $formatted = $date->format("Y-m-d") . 'T' . $date->format("H:i:sP");
        $this->itransfer_timelimit = $formatted;
    }

    public function setMetadata(array $metadata)
    {
    }

    /**
     * @param $urls string[]
     */
    public function setUrls(array $urls)
    {
        if(is_array($urls)){
            foreach (["itransfer_url_returnsuccess", "itransfer_url_return"] as $var){
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
        $this->itransfer_person_email = $customer->get("itransfer_person_email") ;
        $this->itransfer_person_name = $customer->get("itransfer_person_name") ;
        $this->itransfer_person_phone = $customer->get("itransfer_person_phone") ;
        $this->itransfer_body_type = $customer->get("itransfer_body_type") ;
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
            "itransfer_order_description",
            "itransfer_participant_id",
            "itransfer_participant_ident",
            "itransfer_order_id",
            "itransfer_order_amount",
            "itransfer_order_amount_vat",
            "itransfer_order_currency_ident",
            "itransfer_person_email",
            "itransfer_person_name",
            "itransfer_person_phone",
            "itransfer_body_type",
        ];



        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        $data["itransfer_order_quantity"] = 0;

        if(!empty($this->basketItems)) {
            foreach ($this->basketItems as $number=>$item){
                $number += 1;
                foreach ($item->formData() as $key => $value){
                    $data[str_replace("_item_", "_item{$number}_", $key)] = $value;
                    if(stripos($key, "quantity")!==false) $data["itransfer_order_quantity"] += $value;
                }

            }
        }
        else $not_filled[] = "basketItems";

        if(!empty($not_filled)) throw new NotValidOrderData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        $not_required = [
            "itransfer_language_ident",
            "itransfer_timelimit",
            "itransfer_url_returnsuccess",
            "itransfer_url_return",

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
}