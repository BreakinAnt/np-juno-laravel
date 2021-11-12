<?php

namespace App\Juno;

use App\Juno\Services\JunoAPI;
use App\Juno\Models\JunoBilling;
use App\Juno\Models\JunoCharge;
use App\Juno\Models\JunoCreditCardCharge;
use App\Juno\Models\JunoEnvironment;
use App\Juno\Models\JunoPix;
use ErrorException;
use Illuminate\Support\Facades\Log;

class JunoClient extends JunoAPI
{
    /**
     *  
     * @param string $clientId ID do cliente.
     * @param string $clientSecret Secret do cliente.
     * @param string $junoPrivate Chave privada da conta Juno.
     * @param JunoEnvironment $environment Ambiente que vai ser utilizado.
     * 
     * @return JunoClient
    */
    public function __construct(string $clientId,string $clientSecret,string $junoPrivate, int $environment)
    {
        $this->resourceToken = base64_encode($clientId.':'.$clientSecret);
        $this->privateToken = $junoPrivate;
        $this->billing = null;
        $this->charge = null;
        $this->creditCardCharge = null;
        $this->pix = null;
        $this->environment = $environment;
    }

    /**
     * @param JunoBilling $billing
     * 
     * @return JunoClient
     */
    public function setBilling(JunoBilling $billing)
    {
        $this->billing = $billing;

        return $this;
    }

    /**
     * @param JunoCharge $charge
     * 
     * @return JunoClient
     */
    public function setCharge(JunoCharge $charge)
    {
        $this->charge = $charge;

        return $this;
    }

    /**
     * @param JunoCreditCardCharge $creditCardCharge
     * 
     * @return JunoClient
     */
    public function setCreditCardCharge(JunoCreditCardCharge $creditCardCharge)
    {
        $this->creditCardCharge = $creditCardCharge;

        return $this;
    }

    /**
     * Realiza pagamento da cobrança.
     */
    public function createCharge()
    {
        $authToken = $this->getAuthToken($this->resourceToken)->access_token;
        $chargeRes = $this->ApiCreateCharge($this->charge, $this->billing, $authToken);
        $res['charges'] = $chargeRes;

		if($this->creditCardCharge){
            $paymentChargesRes = [];
            $charge = $chargeRes->_embedded->charges[0];
            $this->creditCardCharge->setChargeId($charge->id);
            try {
                array_push($paymentChargesRes, $this->ApiCreatePaymentCharge($this->creditCardCharge, $authToken));
            } catch(ErrorException $e) {
                Log::critical($e);
                $this->ApiCancelCharge($charge->id, $authToken);
                $paymentChargesRes['exceptions'] = [];
                $paymentChargesRes['hasError'] = true;
                array_push($paymentChargesRes['exceptions'], $e->getMessage());
            }
            $res['creditCardCharges'] = $paymentChargesRes;
        }

        return $res;
    }

    public function setPix(JunoPix $pix)
    {
        $this->pix = $pix;
        return $this;
    }

    public function newPix(JunoPix $pix)
    {
        $authToken = $this->getAuthToken($this->resourceToken)->access_token;
        return $this->ApiCreatePixKey($pix, $authToken);
    }

    public function fetchCharge($chargeId)
    {
        $authToken = $this->getAuthToken($this->resourceToken)->access_token;
        return $this->ApiGetCharge($chargeId, $authToken);
    }

    public function createWebhook($url)
    {
        $authToken = $this->getAuthToken($this->resourceToken)->access_token;
        return $this->apiCreateWebhook($url, $authToken);
    }
}