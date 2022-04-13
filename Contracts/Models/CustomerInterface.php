<?php

namespace Invoicebox\Contracts\Models;

interface CustomerInterface{

    const PRIVATE_PERSON = 'private';
    const LEGAL_PERSON = 'legal';

    /**
     * CustomerInterface constructor.
     * @param string|null $type Тип заказчика legal - юр. лицо, private - физ лицо
     * @param string|null $name Фамилия, имя и отчество плательщика или наименование организации
     * @param string|null $phone Номер мобильного телефона плательщика. Используется с целью информирования клиента о сформированном счёте, полученной оплате или оформлении возврата
     * @param string|null $email Адрес электронной почты плательщика. Используется с целью информирования клиента о сформированном счёте, полученной оплате или оформлении возврата
     * @param string|null $inn ИНН
     * @param string|null $address Юр. адрес
     */
    public function __construct(string $type=null, string $name=null, string $phone=null, string $email=null, string $inn=null, string $address=null);
    
    public function setType(string $type);
    
    public function setName(string $name);
    
    public function setPhone(string $phone);
    
    public function setEmail(string $email);
    
    public function setInn(string $inn);
    
    public function setAddress(string $address);

    public function formData(): array;
    
}