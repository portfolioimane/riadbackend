<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSettings extends Model
{
    protected $table = 'smtp_settings';

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'enabled',
    ];



    protected $casts = [
        'enabled' => 'boolean',
    ];
}
