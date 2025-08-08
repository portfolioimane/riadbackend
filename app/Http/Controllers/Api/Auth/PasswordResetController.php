<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\ApiPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Send a password reset link to the given email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        Log::info('Password reset request received', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            Log::warning('Password reset request failed validation', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Validation passed, sending reset link...');

        // Use the custom notification by specifying it in the broker configuration
        $status = Password::broker()->sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                // Send the custom notification
                $user->notify(new ApiPasswordResetNotification($token));
            }
        );

        Log::info('Password::sendResetLink status', ['status' => $status]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Password reset link sent successfully.',
                'status' => 'success'
            ]);
        } else {
            Log::error('Failed to send password reset link', ['status' => $status]);
            return response()->json([
                'message' => 'Unable to send password reset link. Please try again.',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        Log::info('Password reset submission received', ['request' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Password reset validation failed', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Validation passed, attempting password reset...');

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                Log::info('Resetting password for user', ['user_id' => $user->id]);
                
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Fire the password reset event
                event(new PasswordReset($user));
            }
        );

        Log::info('Password::reset status', ['status' => $status]);

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.',
                'status' => 'success'
            ]);
        } else {
            Log::error('Password reset failed', ['status' => $status]);
            return response()->json([
                'message' => 'Password reset failed. Please check your token and try again.',
                'status' => 'error'
            ], 500);
        }
    }
}