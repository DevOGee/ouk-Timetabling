<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function emailSetup()
    {
        $settings = [
            'MAIL_MAILER'       => env('MAIL_MAILER', 'smtp'),
            'MAIL_HOST'         => env('MAIL_HOST', 'smtp.gmail.com'),
            'MAIL_PORT'         => env('MAIL_PORT', 587),
            'MAIL_USERNAME'     => env('MAIL_USERNAME', ''),
            'MAIL_PASSWORD'     => env('MAIL_PASSWORD', ''),
            'MAIL_ENCRYPTION'   => env('MAIL_ENCRYPTION', 'tls'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', ''),
            'MAIL_FROM_NAME'    => env('MAIL_FROM_NAME', 'OUK Timetabling'),
        ];

        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmailSetup(Request $request)
    {
        $validated = $request->validate([
            'MAIL_HOST'         => ['required', 'string'],
            'MAIL_PORT'         => ['required', 'integer', 'in:25,465,587,2525'],
            'MAIL_USERNAME'     => ['required', 'email'],
            'MAIL_PASSWORD'     => ['required', 'string'],
            'MAIL_ENCRYPTION'   => ['required', 'in:tls,ssl,none'],
            'MAIL_FROM_ADDRESS' => ['required', 'email'],
            'MAIL_FROM_NAME'    => ['required', 'string', 'max:100'],
        ]);

        $this->writeToEnv([
            'MAIL_MAILER'       => 'smtp',
            'MAIL_HOST'         => $validated['MAIL_HOST'],
            'MAIL_PORT'         => $validated['MAIL_PORT'],
            'MAIL_USERNAME'     => $validated['MAIL_USERNAME'],
            'MAIL_PASSWORD'     => $validated['MAIL_PASSWORD'],
            'MAIL_ENCRYPTION'   => $validated['MAIL_ENCRYPTION'],
            'MAIL_FROM_ADDRESS' => $validated['MAIL_FROM_ADDRESS'],
            'MAIL_FROM_NAME'    => '"' . $validated['MAIL_FROM_NAME'] . '"',
        ]);

        Artisan::call('config:clear');

        return redirect()->route('admin.settings.email')
                         ->with('success', 'Email settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            Mail::raw(
                "This is a test email from the OUK Timetabling System.\n\nIf you received this, your email configuration is working correctly.",
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('OUK Timetabling — Test Email');
                }
            );

            return back()->with('test_success', "Test email sent to {$request->test_email} successfully.");
        } catch (\Exception $e) {
            return back()->with('test_error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    private function writeToEnv(array $values): void
    {
        $envPath = base_path('.env');
        $env = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            // If the key exists, replace its value
            if (preg_match("/^{$key}=.*/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
            } else {
                // Append the key if it doesn't exist
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $env);
    }
}
