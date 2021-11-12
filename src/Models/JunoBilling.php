<?php

namespace App\Juno\Models;

class JunoBilling {
    /**
     * @param array $billing Dados para montar o Billing.
     * Documentação: https://dev.juno.com.br/api/v2#operation/createCharge
     */
    public function __construct(array $billing = [])
    {
        if(isset($billing['name'])){
            $this->name = $billing['name'];
        } else {
            throw new \InvalidArgumentException('Missing "name" parameter');
        }

        if(isset($billing['document'])){
            $this->document = $billing['document'];
        } else {
            throw new \InvalidArgumentException('Missing "document" parameter');
        }

        if(isset($billing['email'])){
            $this->email = $billing['email'];
        } else {
            throw new \InvalidArgumentException('Missing "email" parameter');
        }

        if(isset($billing['address'])){
            $this->address = $billing['address'];
        } else {
            throw new \InvalidArgumentException('Missing "address" parameter');
        }

        $this->secondaryEmail = $billing['secondaryEmail'] ?? null;
        $this->phone = $billing['phone'] ?? null;
        $this->birthDate = $billing['birthDate'] ?? null;
        $this->notify = $billing['notify'] ?? null;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function setDocument(string $document)
    {
        $this->document = $document;

        return $this;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function setBirthDate(string $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function setNotify(bool $notify)
    {
        $this->notify = $notify;

        return $this;
    }
}