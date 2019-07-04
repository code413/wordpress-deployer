<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    protected $with = ['replacements'];

    public function replacements()
    {
        return $this->hasMany(Replacement::class);
    }

    public function getPasswordAttribute()
    {
        return $this->decrypt($this->db_password);
    }

    protected function decrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // decode
        $string = base64_decode($string);
        // decrypt
        return openssl_decrypt($string, $method, $key, 0, $iv);
    }
}
