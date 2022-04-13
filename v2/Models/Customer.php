<?php

namespace Invoicebox\V2\Models;

use Invoicebox\Contracts\Models\CustomerInterface;
use Invoicebox\Exceptions\NotExistPropertyException;
use Invoicebox\Exceptions\NotValidCustomerData;

class Customer implements CustomerInterface
{

    private $itransfer_body_type;
    private $itransfer_person_name;
    private $itransfer_person_email;
    private $itransfer_person_phone;

    /**
     * Customer constructor.
     * @param string|null $type
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $email
     * @param string|null $inn
     * @param string|null $address
     * @throws NotValidCustomerData
     */
    public function __construct(string $type=null, string $name=null, string $phone=null, string $email=null, string $inn=null, string $address=null)
    {
        if(!is_null($type)) $this->setType($type);
        if(!is_null($name)) $this->setName($name);
        if(!is_null($phone)) $this->setPhone($phone);
        if(!is_null($email)) $this->setEmail($email);
        if(!is_null($inn)) $this->setInn($inn);
        if(!is_null($address)) $this->setAddress($address);
    }

    public function get($propertyName){
        if(property_exists($this, $propertyName)) return $this->$propertyName;
        else throw new NotExistPropertyException($propertyName);
    }

    public function setType(string $type)
    {
        switch ($type){
            case self::PRIVATE_PERSON: $this->itransfer_body_type = self::PRIVATE_PERSON; break;
            case self::LEGAL_PERSON: $this->itransfer_body_type = self::LEGAL_PERSON; break;
            default: throw new NotValidCustomerData("Тип клиента задан неправильно");
        }
    }

    public function setName(string $name)
    {
        if(empty($name)) throw new NotValidCustomerData("Имя является обязательным полем");
        $this->itransfer_person_name = $name;
    }

    public function setPhone(string $phone)
    {
        if(empty($phone)) throw new NotValidCustomerData("Телефон является обязательным полем");
        $this->itransfer_person_phone = $phone;
    }

    public function setEmail(string $email)
    {
        if(empty($email)) throw new NotValidCustomerData("Email является обязательным полем");
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new NotValidCustomerData("Email имеет неправильный формат");
        $this->itransfer_person_email = $email;
    }

    public function setInn(string $inn)
    {
        // doesn't used in v2
    }

    public function setAddress(string $address)
    {
        // doesn't used in v2
    }

    /**
     * @return array
     * @throws NotValidCustomerData
     */
    public function formData(): array
    {
        $data = [];
        $required_fields = [
            "itransfer_body_type",
            "itransfer_person_name",
            "itransfer_person_email",
            "itransfer_person_phone",
        ];

        $not_filled = [];

        foreach ($required_fields as $field) {
            if(is_null($this->$field)) $not_filled[] = $field;
            else $data[$field] = $this->$field;
        }

        if(!empty($not_filled)) throw new NotValidCustomerData(sprintf("Значения полей %s должны быть заполнены.", implode(", ", $not_filled)));

        return $data;
    }
}
