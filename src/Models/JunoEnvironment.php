<?php
namespace App\Juno\Models;

class JunoEnvironment 
{
    const SANDBOX = 0;
    const PRODUCTION = 1;

    public static function getUrl(int $type)
    {
        switch($type){
            case 1:
                return 'https://api.juno.com.br/';
            break;
            default:
            return 'https://sandbox.boletobancario.com/api-integration/';
        }
    }
}