<?php

namespace felipe\BrediJuno\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class JunoPix extends Model
{
    private $idempotencyKey, $type;
    public function __construct()
    {
        $this->key = null;
        $this->type = 'RANDOM_KEY';
        $this->idempotencyKey = Uuid::uuid4()->serialize();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($value)
    {
        $this->key = $value;
        return $this;
    }

    public function getIdempotencyKey()
    {
        return $this->idempotencyKey;
    }

    public function toJson($options = 0)
    {
        return [
            'type' => $this->type,
        ];
    }
}
