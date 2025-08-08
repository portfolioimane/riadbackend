<?php
namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Models\PaymentSettings;
use App\Http\Controllers\Controller;


class PaymentSettingsController extends Controller
{
    public function index()
    {
        return PaymentSettings::all();
    }

    public function show($id)
    {
        return PaymentSettings::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'enabled'    => 'boolean',
        ]);

        $setting = PaymentSettings::findOrFail($id);
        $setting->update($data);

        return response()->json(['message' => 'Payment setting updated', 'data' => $setting]);
    }
}
