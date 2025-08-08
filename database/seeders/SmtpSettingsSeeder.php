<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmtpSettings;

class SmtpSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SmtpSettings::create([
            'mailer'       => 'smtp',
            'host'         => 'smtp.gmail.com',
            'port'         => 587,
            'username'     => 'your-email@gmail.com',
            'password'     => 'your-email-password-or-app-password',
            'encryption'   => 'tls',
            'from_address' => 'your-email@gmail.com',
            'from_name'    => 'Your Company Name',
            'enabled'      => false,
        ]);
    }
}
