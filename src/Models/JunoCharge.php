<?php

namespace felipe\BrediJuno\Models;

use App\Juno\JunoPayment;

//doc: https://dev.juno.com.br/api/v2#operation/createCharge
class JunoCharge
{
    public function __construct(array $data = [])
    {
        if(!isset($data['description'])){
            throw new \InvalidArgumentException('Missing "description" parameter');
        }
        if(!isset($data['amount']) && !isset($data['totalAmount'])){
            throw new \InvalidArgumentException('Missing "amount" parameter');
        }
        if(!isset($data['pixKey']) && isset($data['paymentType'])){
            if($data['paymentType'] == JunoPayment::pix){
                throw new \InvalidArgumentException('Parameter "pixKey" required if "paymentType" is specified as "boleto_pix');
            }
        }
        if(isset($data['amount']) && isset($data['totalAmount'])){
            throw new \InvalidArgumentException('You cannot specify both "amount" and "totalAmount" at the same time');
        }

        if(isset($data['totalAmount']) && !isset($data['installments'])){
            throw new \InvalidArgumentException('"installments" must be specified if "totalAmount" is set');
        }

        $this->pixKey = $data['pixKey'] ?? null;
        $this->pixIncludeImage = $data['pixIncludeImage'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->references = $data['references'] ?? null;
        $this->totalAmount = $data['totalAmount'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->dueDate = $data['dueDate'] ?? null;
        $this->installments = $data['installments'] ?? null;
        $this->maxOverdueDays = $data['maxOverdueDays'] ?? null;
        $this->fine = $data['fine'] ?? null;
        $this->interest = $data['interest'] ?? null;
        $this->discountAmount = $data['discountAmount'] ?? null;
        $this->discountDays = $data['discountDays'] ?? null;
        $this->paymentTypes = $data['paymentTypes'] ?? null;
        $this->paymentAdvance = $data['paymentAdvance'] ?? null;
        $this->feeSchemaToken = $data['feeSchemaToken'] ?? null;
        $this->split = $data['split'] ?? null;
    }

    /**
     * @param int $type 0 = Cartão de Credito; 1 = Boleto; 2 = PIX.
     * 
     * @return Eloquent Retorna objeto do banco com o token.
    */
    public function setPaymentType(int $type)
    {
        switch($type){
            case 0:
                $this->paymentType = JunoPayment::creditCard;
            break;
            case 1: 
                $this->paymentType = JunoPayment::boleto;
            break;
            case 2:
                $this->paymentType = JunoPayment::pix;
            break;
            default:
                $this->paymentType = JunoPayment::creditCard;
        }

        return $this;
    }
}