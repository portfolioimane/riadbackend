<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSettings extends Model
{
    protected $fillable = [
        'type',
        'public_key',
        'secret_key',
        'api_url',
        'enabled',
    ];
}
