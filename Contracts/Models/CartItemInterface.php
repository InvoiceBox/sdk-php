<?php

namespace Invoicebox\Contracts\Models;

interface CartItemInterface{

    const SERVICE_TYPE = "service";
    const PRODUCT_TYPE = "commodity";
    const VAT_CODE_VATNONE = "VATNONE";
    const VAT_CODE_RUS_VAT0 = "RUS_VAT0";
    const VAT_CODE_RUS_VAT10 = "RUS_VAT10";
    const VAT_CODE_RUS_VAT20 = "RUS_VAT20";
    const PAYMENT_TYPE_PREPAYMENT = "prepayment";
    const PAYMENT_TYPE_FULL_PREPAYMENT = "full_prepayment";
    const PAYMENT_TYPE_FULL_ADVANCE = "advance";
    const PAYMENT_TYPE_FULL_PAYMENT = "full_payment";

    /**
     * CartItemInterface constructor.
     * @param string|null $sku Артикул, например: 5fe0adcfa7fb4
     * @param string|null $id Уникальный идентификатор позиции в заказе
     * @param string|null $quantity Количество товара или услуги
     * @param string|null $name Наименование товара или услуги
     * @param bool $isOkei Выполнять ли проверку соответствия единицы измерения по справочнику ОКЕИ
     * @param string|null $measure Единица измерения (для России - по ОКЕИ), например шт.
     * @param string|null $measureCode Код единицы измерения (для России - по ОКЕИ), например 796
     * @param string|null $originCountry Страна происхождения товара, например, Россия
     * @param string|null $originCountryCode Код страны происхождения, например, Россия 643
     * @param float|null $grossWeight Вес брутто, например 125.45
     * @param float|null $netWeight Вес нетто, например 125.45
     * @param float|null $amount Стоимость единицы, например 100.55
     * @param float|null $amountWoVat Стоимость единицы без учета НДС
     * @param float|null $itemVatRate Значение ставки налога на добавленную стоимость (НДС)2
     * @param float|null $itemVat Сумма налога на добавленную стоимость (НДС) за 1 единицу
     * @param float|null $totalAmount Стоимость всех единиц с НДС, например 123.55
     * @param float|null $totalVatAmount Сумма НДС всех позиций, например 23
     * @param string|null $excise Сумма акциза, например, 10.00
     * @param string|null $type Тип позиции, в соответствии со справочником или service - сервис, commodity - товар
     * @param string|null $paymentType Тип оплаты, допустимые значения: full_prepayment, prepayment, advance, full_payment
     * @param string|null $vatCode Код процента НДС, допустимые значения: VATNONE - не облагается,VATNONE - не облагается, RUS_VAT0 - 0%, RUS_VAT10 - 10%, RUS_VAT20 - 20%
     * @param array|null $metaData Дополнительные данные элемента корзины (для 3й версии апи)
     */
    public function __construct(string $sku=null, string $id=null, string $quantity=null, string $name=null, bool $isOkei=true, string $measure=null, string $measureCode=null,
                                string $originCountry=null, string $originCountryCode=null, float $grossWeight=null, float $netWeight=null,
                                float $amount=null, float $amountWoVat=null, float $itemVatRate=null, float $itemVat=null,
                                float $totalAmount=null, float $totalVatAmount=null, string $excise=null, string $type=null, string $paymentType=null, string $vatCode=null, array $metaData=null);
    
    public function setItemId(string $id);

    public function setSKU(string $sku);

    public function setName(string $name);

    public function setQuantity(float $quantity);

    public function setItemAmount(float $amount);

    public function setMeasure(string $measure, bool $isOkei=true);

    public function setMeasureCode(string $code, bool $isOkei=true);

    public function setType(string $type);

    public function setItemVatRate(float $rate);

    public function setItemVat(float $vat);

    public function setOriginCountry(string $country);

    public function setOriginCountryCode(string $countryCode);

    public function setTotalAmount(float $amount);

    public function setTotalVatAmount(float $vatAmount);

    public function setExciseSum(float $exciseSum);

    public function setVatCode(string $vatCode);

    public function setPaymentType(string $paymentType);

    public function setGrossWeight(float $weight);

    public function setNetWeight(float $weight);

    public function setMetaData(array $metaData);

    public function formData() : array;
    
}