<?php

namespace Invoicebox\V2\Models;

require_once __DIR__ . "/../../resources/OKEIDictionary.php";
use Invoicebox\Contracts\Models\CartItemInterface;
use Invoicebox\Exceptions\NotValidCartItemData;
use Invoicebox\Resources\OKEIDictionary;

class CartItem implements CartItemInterface
{
    private $itransfer_item_ident;
    private $itransfer_item_name;
    private $itransfer_item_quantity;
    private $itransfer_item_measure;
    private $itransfer_item_type;
    private $itransfer_item_price;
    private $itransfer_item_vatrate;
    private $itransfer_item_vat;

    public function __construct(string $sku=null, string $id=null, string $quantity=null, string $name=null, bool $isOkei=true, string $measure=null, string $measureCode=null,
                                string $originCountry=null, string $originCountryCode=null, float $grossWeight=null, float $netWeight=null,
                                float $amount=null, float $amountWoVat=null, float $itemVatRate=null, float $itemVat=null,
                                float $totalAmount=null, float $totalVatAmount=null, string $excise=null, string $type=null, string $paymentType=null, string $vatCode=null, array $metaData=null)
    {
        if(!is_null($id)) $this->setItemId($id);
        if(!is_null($name)) $this->setName($name);
        if(!is_null($measure)) $this->setMeasure($measure, $isOkei);
        if(!is_null($quantity)) $this->setQuantity($quantity);
        if(!is_null($amount)) $this->setItemAmount($amount);
        if(!is_null($totalAmount)) $this->setItemVatRate($itemVatRate);
        if(!is_null($totalAmount)) $this->setItemVat($itemVat);
        if(!is_null($totalVatAmount)) $this->setTotalVatAmount($totalVatAmount);
        if(!is_null($type)) $this->setType($type);
    }

    public function setSKU(string $sku)
    {
        // doesn't used in v2
    }

    public function setItemId(string $id)
    {
        $this->itransfer_item_ident = $id;
    }

    public function setName(string $name)
    {
        if(empty($name)) throw new NotValidCartItemData("Название товара является обязательным полем");
        $this->itransfer_item_name = $name;
    }

    public function setQuantity(float $quantity)
    {
        $this->itransfer_item_quantity = $quantity;
    }

    public function setMeasure(string $measure, bool $isOkei=true)
    {
        $measure = trim($measure);
        if(empty($measure)) throw new NotValidCartItemData("Единица измерения является обязательным полем");
        if($isOkei && !OKEIDictionary::measureExist($measure)) throw new NotValidCartItemData("Единица измерения имеет неправильный формат");

        $this->itransfer_item_measure = $measure;
    }

    public function setMeasureCode(string $code, bool $isOkei=true)
    {
        // doesn't used in v2
    }

    public function setType(string $type)
    {
        switch ($type){
            case self::SERVICE_TYPE: $this->itransfer_item_type = self::SERVICE_TYPE; break;
            case self::PRODUCT_TYPE: $this->itransfer_item_type = self::PRODUCT_TYPE; break;
            default: throw new NotValidCartItemData("Тип позиции задан неправильно");
        }
    }

    public function setItemAmount(float $amount)
    {
        $this->itransfer_item_price = $amount;
    }

    public function setItemAmountWithoutVat(float $amount)
    {
        // doesn't used in v2
    }

    public function setItemVatRate(float $rate)
    {
        $this->itransfer_item_vatrate = $rate;
    }

    public function setItemVat(float $vat)
    {
        $this->itransfer_item_vat = $vat;
    }

    public function setOriginCountry(string $country)
    {
        // doesn't used in v2
    }

    public function setOriginCountryCode(string $countryCode)
    {
        // doesn't used in v2
    }

    public function setTotalAmount(float $amount)
    {
        // doesn't used in v2
    }


    public function setTotalVatAmount(float $vatAmount)
    {
        // doesn't used in v2
    }

    public function setExciseSum(float $exciseSum)
    {
        // doesn't used in v2
    }

    public function setVatCode(string $vatCode)
    {
        // doesn't used in v2
    }

    public function setPaymentType(string $paymentType)
    {
        // doesn't used in v2
    }

    public function setGrossWeight(float $weight){
        // doesn't used in v2
    }

    public function setNetWeight(float $weight){
        // doesn't used in v2
    }

    public function setMetaData(array $metaData)
    {
        // doesn't used in v2
    }

    public function formData(): array
    {
        $data = [];
        $required_fields = [
            "itransfer_item_name",
            "itransfer_item_quantity",
            "itransfer_item_measure",
            "itransfer_item_type",
            "itransfer_item_price",
            "itransfer_item_vatrate",
            "itransfer_item_vat",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidCartItemData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        $not_required = [
            "itransfer_item_ident",
        ];

        foreach ($not_required as $field) {
            if(!is_null($this->$field)) $data[$field] = $this->$field;
        }
        return $data;
    }
}
