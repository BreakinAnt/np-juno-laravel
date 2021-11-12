<?php

namespace felipe\BrediJuno\Models;

abstract class JunoPayment {
    const creditCard = 'CREDIT_CARD';
    const boleto = 'BOLETO';
    const pix = 'BOLETO_PIX';
}