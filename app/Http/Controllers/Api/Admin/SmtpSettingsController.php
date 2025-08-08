<?php
namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\SmtpSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class SmtpSettingsController extends Controller
{
    public function index()
    {
        return SmtpSettings::all();
    }

    public function show($id)
    {
        return SmtpSettings::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'mailer'       => 'nullable|string',
            'host'         => 'nullable|string',
            'port'         => 'nullable|integer',
            'username'     => 'nullable|string',
            'password'     => 'nullable|string', // Stored in plain text
            'encryption'   => 'nullable|string',
            'from_address' => 'nullable|email',
            'from_name'    => 'nullable|string',
            'enabled'      => 'boolean',
        ]);

        $smtp = SmtpSettings::findOrFail($id);
        $smtp->update($data);

        // Restart queue workers to pick up new SMTP configuration
        try {
            Artisan::call('queue:restart');
        } catch (\Exception $e) {
            // Log the error but don't fail the update
            \Log::warning('Failed to restart queue workers: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'SMTP setting updated and queue workers restarted', 
            'data' => $smtp
        ]);
    }
}