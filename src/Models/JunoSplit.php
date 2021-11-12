<?php
namespace App\Juno\Models;

class JunoSplit
{
    public function __construct(string $recipientToken, bool $amountRemainder, bool $chargeFree)
    {
        $this->recipientToken = $recipientToken;
        $this->amountRemainder = $amountRemainder;
        $this->chargeFree = $chargeFree;
        $this->amount = null;
        $this->percentage = null;
    }

    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        $this->percentage = null;

        return $this;
    }

    public function setPercentage(int $percentage)
    {
        $this->percentage = $percentage;
        $this->amount = null;

        return $this;
    }
}
