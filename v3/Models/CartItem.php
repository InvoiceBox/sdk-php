<?php

namespace Invoicebox\V3\Models;

require_once __DIR__ . "/../../resources/OKEIDictionary.php";

use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Exceptions\NotValidCartItemData;
use Invoicebox\Resources\OKEIDictionary;

class CartItem implements CartItemInterface
{
    private $sku;
    private $name;
    private $measure;
    private $measureCode;
    private $originCountry;
    private $originCountryCode;
    private $grossWeight;
    private $netWeight;
    private $quantity;
    private $amount;
    private $amountWoVat;
    private $totalAmount;
    private $totalVatAmount;
    private $excise;
    private $vatCode;
    private $type;
    private $paymentType;
    private $metaData;

    public function __construct(string $sku=null, string $id=null, string $quantity=null, string $name=null, bool $isOkei=true, string $measure=null, string $measureCode=null,
                                string $originCountry=null, string $originCountryCode=null, float $grossWeight=null, float $netWeight=null,
                                float $amount=null, float $amountWoVat=null, float $itemVatRate=null, float $itemVat=null,
                                float $totalAmount=null, float $totalVatAmount=null, string $excise=null, string $type=null, string $paymentType=null, string $vatCode=null, array $metaData=null)
    {
        if(!is_null($sku)) $this->setSKU($sku);
        if(!is_null($name)) $this->setName($name);
        if(!is_null($measure)) $this->setMeasure($measure, $isOkei);
        if(!is_null($measureCode)) $this->setMeasureCode($measureCode, $isOkei);
        if(!is_null($originCountry)) $this->setOriginCountry($originCountry);
        if(!is_null($originCountryCode)) $this->setOriginCountryCode($originCountryCode);
        if(!is_null($grossWeight)) $this->setGrossWeight($grossWeight);
        if(!is_null($netWeight)) $this->setNetWeight($netWeight);
        if(!is_null($quantity)) $this->setQuantity($quantity);
        if(!is_null($amount)) $this->setItemAmount($amount);
        if(!is_null($amountWoVat)) $this->setItemAmountWithoutVat($amountWoVat);
        if(!is_null($totalAmount)) $this->setTotalAmount($totalAmount);
        if(!is_null($totalVatAmount)) $this->setTotalVatAmount($totalVatAmount);
        if(!is_null($excise)) $this->setExciseSum($excise);
        if(!is_null($vatCode)) $this->setVatCode($vatCode);
        if(!is_null($type)) $this->setType($type);
        if(!is_null($paymentType)) $this->setPaymentType($paymentType);
        if(!is_null($metaData)) $this->setMetaData($metaData);
    }

    public function setSKU(string $sku)
    {
        $this->sku = $sku;
    }

    public function setItemId(string $id)
    {
        // doesn't used in v3
    }

    public function setName(string $name)
    {
        if(empty($name)) throw new NotValidCartItemData("Название товара является обязательным полем");
        if(strlen($name) > 500) throw new NotValidCartItemData("Название товара не должно быть длиннее 500 знаков");
        $this->name = $name;
    }

    public function setQuantity(float $quantity)
    {
        $this->quantity = $quantity;
    }

    public function setMeasure(string $measure, bool $isOkei=true)
    {
        $measure = trim(trim($measure), ".");
        if(empty($measure)) throw new NotValidCartItemData("Единица измерения является обязательным полем");
        if($isOkei && !OKEIDictionary::measureExist($measure)) throw new NotValidCartItemData("Единица измерения имеет неправильный формат");
        $code = OKEIDictionary::getCodeByMeasure($measure);
        if($isOkei && !$code) throw new NotValidCartItemData("Единица измерения имеет неправильный формат");

        $this->measure = $measure;
        if(empty($this->measureCode) && $isOkei && $code) $this->measureCode = $code;
    }

    public function setMeasureCode(string $code, bool $isOkei=true)
    {
        $code = trim($code);
        if(empty($code)) throw new NotValidCartItemData("Код единицы измерения является обязательным полем");
        if($isOkei && !OKEIDictionary::codeExist($code)) throw new NotValidCartItemData("Код единицы измерения имеет неправильный формат");
        $measure = OKEIDictionary::getMeasureByCode($code);
        if($isOkei && !$measure) throw new NotValidCartItemData("Код единицы измерения имеет неправильный формат");

        if(empty($this->measure) && $isOkei && $measure) $this->measure = $measure;
        $this->measureCode = $code;
    }

    public function setType(string $type)
    {
        switch ($type){
            case self::SERVICE_TYPE: $this->type = self::SERVICE_TYPE; break;
            case self::PRODUCT_TYPE: $this->type = self::PRODUCT_TYPE; break;
            default: throw new NotValidCartItemData("Тип позиции задан неправильно");
        }
    }

    public function setItemAmount(float $amount)
    {
        $this->amount = $amount;
    }

    public function setItemAmountWithoutVat(float $amount)
    {
        $this->amountWoVat = $amount;
    }

    public function setItemVatRate(float $rate)
    {
        // doesn't used in v3
    }

    public function setItemVat(float $vat)
    {
        // doesn't used in v3
    }

    public function setOriginCountry(string $country)
    {
        $this->originCountry = $country;
    }

    public function setOriginCountryCode(string $countryCode)
    {
        $this->originCountryCode = $countryCode;
    }

    /**
     * Стоимость всех единиц с НДС, например 123.55
     * @param float $amount
     */
    public function setTotalAmount(float $amount)
    {
        $this->totalAmount = $amount;
    }

    /**
     * Сумма НДС всех позиций, например 23
     * @param float $vatAmount
     */
    public function setTotalVatAmount(float $vatAmount)
    {
        $this->totalVatAmount = $vatAmount;
    }

    /**
     * Сумма акциза, например, 10.00
     * @param float $exciseSum
     */
    public function setExciseSum(float $exciseSum)
    {
        $this->excise = $exciseSum;
    }

    public function setVatCode(string $vatCode)
    {
        switch ($vatCode){
            case self::VAT_CODE_VATNONE: $this->vatCode = self::VAT_CODE_VATNONE; break;
            case self::VAT_CODE_RUS_VAT0: $this->vatCode = self::VAT_CODE_RUS_VAT0; break;
            case self::VAT_CODE_RUS_VAT10: $this->vatCode = self::VAT_CODE_RUS_VAT10; break;
            case self::VAT_CODE_RUS_VAT20: $this->vatCode = self::VAT_CODE_RUS_VAT20; break;
            default: throw new NotValidCartItemData("Код процента НДС задан неправильно");
        }
    }

    public function setPaymentType(string $paymentType)
    {
        switch ($paymentType){
            case self::PAYMENT_TYPE_FULL_PAYMENT: $this->paymentType = self::PAYMENT_TYPE_FULL_PAYMENT; break;
            case self::PAYMENT_TYPE_PREPAYMENT:  $this->paymentType = self::PAYMENT_TYPE_PREPAYMENT; break;
            case self::PAYMENT_TYPE_FULL_ADVANCE: $this->paymentType = self::PAYMENT_TYPE_FULL_ADVANCE; break;
            case self::PAYMENT_TYPE_FULL_PREPAYMENT: $this->paymentType = self::PAYMENT_TYPE_FULL_PREPAYMENT; break;
            default: throw new NotValidCartItemData("Тип оплаты задан неправильно");
        }
    }

    public function setGrossWeight(float $weight){
        $this->grossWeight = $weight;
    }

    public function setNetWeight(float $weight){
        $this->netWeight = $weight;
    }

    public function formData(): array
    {
        $data = [];
        $required_fields = [
            "sku",
            "name",
            "measure",
            "measureCode",
            "quantity",
            "amount",
            "amountWoVat",
            "totalAmount",
            "totalVatAmount",
            "vatCode",
            "type",
            "paymentType",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidCartItemData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        $not_required = [
            "originCountry",
            "originCountryCode",
            "grossWeight",
            "netWeight",
            "excise",
            "metaData",
        ];

        foreach ($not_required as $field) {
            if(!is_null($this->$field)) $data[$field] = $this->$field;
        }
        return $data;
    }

    public function setMetaData(array $metaData)
    {
        $this->metaData = $metaData;
    }
}
