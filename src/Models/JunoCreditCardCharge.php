<?php

namespace App\Juno\Models;

class JunoCreditCardCharge 
{
    public function __construct($data)
    {
        if(!isset($data['billing']['email'])){
            throw new \InvalidArgumentException('Parameter "email" must be provided');
        }
        if(!isset($data['billing']['address'])){
            throw new \InvalidArgumentException('Parameter "address" must be provided');
        }
        if(!isset($data['billing']['address']['street'])){
            throw new \InvalidArgumentException('Parameter "street" must be provided');
        }
        if(!isset($data['billing']['address']['number'])){
            throw new \InvalidArgumentException('Parameter "number" must be provided.');
        }
        if(!isset($data['billing']['address']['city'])){
            throw new \InvalidArgumentException('Parameter "city" must be provided');
        }
        if(!isset($data['billing']['address']['state'])){
            throw new \InvalidArgumentException('Parameter "state" must be provided');
        }
        if(!isset($data['billing']['address']['postCode'])){
            throw new \InvalidArgumentException('Parameter "postCode" must be provided');
        }
        if(isset($data['creditCardDetails']['creditCardId']) && isset($data['creditCardDetails']['creditCardHash'])){
            throw new \InvalidArgumentException('You cannot specify both "creditCardHash" and "creditCardDetails" at the same time');
        }

        $this->chargeId = null;
        $this->billing = $data['billing'];
        $this->creditCardDetails = $data['creditCardDetails'];
    }    

    public function setChargeId($chargeId){
        $this->chargeId = $chargeId;
    }
}