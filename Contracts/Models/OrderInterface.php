<?php
namespace Invoicebox\Contracts\Models;

use Invoicebox\V3\Models\CartItem;

interface OrderInterface{

    /**
     * OrderInterface constructor.
     * @param string|null $merchantId Идентификатор магазина
     * @param string|null $merchantCode Региональный код магазина
     * @param string|null $description Описание заказа или назначение платежа
     * @param int|null $merchantOrderId Идентификатор заказа в учётной системе магазина
     * @param float|null $amount Сумма заказа
     * @param float|null $vatAmount Сумма НДС
     * @param string|null $currencyId Код валюты заказа в соответствии с ISO 4217
     * @param string|null $languageId Язык интерфейса платежной страницы
     * @param \DateTime|null $expirationDate Срок действия заказа
     * @param CartItem[] $basketItems Корзина заказа
     * @param array|null $metaData Дополнительные данные заказа
     * @param CustomerInterface|null $customer Информация о заказчике
     * @param string|null $notificationUrl U(v3) RL для отправки уведомлений об изменениях статуса заказа, по умолчанию используется URL из настроек магазина
     * @param string|null $successUrl (v2|v3) Ссылка для перехода на сайт Магазина в случае успешной оплаты
     * @param string|null $failUrl (v3) Ссылка для перехода на сайт Магазина в случае ошибки оплаты
     * @param string|null $returnUrl (v2|v3) Ссылка для возврата на сайт Магазина
     * @param bool $customerLocked Запретить изменение реквизитов плательщика (всех)
     * @param array $customerLockedFields Набор полей из Customer, которые требуется запретить для редактирования на платежной странице
     */
    public function __construct(string $merchantId=null, string $merchantCode=null, string $description=null, int $merchantOrderId=null,
                                float $amount=null, float $vatAmount=null, string $currencyId=null, string $languageId=null,
                                \DateTime $expirationDate=null, array $basketItems=[], array $metaData=null, CustomerInterface $customer=null,
                                string $notificationUrl=null, string $successUrl=null, string $failUrl=null, string $returnUrl=null,
                                bool $customerLocked=false, array $customerLockedFields=[]);

    public function setMerchantId(string $id);

    public function setMerchantCode(string $code);

    public function setDescription(string $description);

    public function setOrderId(string $id);

    public function setTotalAmount(float $amount);

    public function setTotalVatAmount(float $vatAmount);

    public function setCurrency(string $currency);

    public function setLanguage(string $language);

    public function setExpirationDate(\DateTime $date);

    public function setMetadata(array $metadata);

    public function setUrls(array $urls);

    public function setTest(bool $test);

    /**
     * @param $basket CartItemInterface[]
     */
    public function setBasket(array $basket);

    /**
     * @param CustomerInterface $customer
     * @return mixed
     */
    public function setCustomerData(CustomerInterface $customer);

    public function formData() : array;

}