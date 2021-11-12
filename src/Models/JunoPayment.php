<?php

namespace App\Juno\Models;

abstract class JunoPayment {
    const creditCard = 'CREDIT_CARD';
    const boleto = 'BOLETO';
    const pix = 'BOLETO_PIX';
}