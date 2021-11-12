<?php

namespace App\Juno\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class JunoToken extends Model
{
    protected $fillable = ['access_token', 'bearer', 'expires_in', 'scope', 'user_name', 'jti'];

    public function checkTime()
    {
        $fetchedTimestamp = (new DateTime($this->created_at))->getTimestamp();
        $timelimit = $this->expires_in - 1800;

        $dif = time() - $fetchedTimestamp;

        return $dif > $timelimit;
    }
}