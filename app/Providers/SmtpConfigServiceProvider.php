<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SmtpSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class SmtpConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        try {
            // Check if the smtp_settings table exists before querying
            if (!Schema::hasTable('smtp_settings')) {
                return;
            }

            // Load the SMTP settings from DB (first record or however you want)
            $smtp = SmtpSettings::where('enabled', true)->first();
            
            if ($smtp) {
                // Set the default mailer to smtp when SMTP settings are found
                Config::set('mail.default', 'smtp');
                
                // Configure SMTP mailer settings
                Config::set('mail.mailers.smtp.transport', 'smtp');
                Config::set('mail.mailers.smtp.host', $smtp->host);
                Config::set('mail.mailers.smtp.port', (int) $smtp->port);
                Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
                Config::set('mail.mailers.smtp.username', $smtp->username);
                Config::set('mail.mailers.smtp.password', $smtp->password);
                
                // Set global from address
                Config::set('mail.from.address', $smtp->from_address);
                Config::set('mail.from.name', $smtp->from_name);
                
            
            }
        } catch (\Exception $e) {
            // Log the error but don't break the application
            \Log::error('SMTP Configuration Error: ' . $e->getMessage());
        }
    }
}