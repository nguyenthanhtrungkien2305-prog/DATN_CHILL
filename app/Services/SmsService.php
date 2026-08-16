<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Gửi mã OTP đến Số điện thoại của người dùng (Hỗ trợ TextBee, Twilio, Log System)
     */
    public static function sendOtp($phone, $otp, $email = null)
    {
        $message = "Ma OTP xac thuc tai Chill Chill Coffee la: {$otp}. Ma co hieu luc trong 5 phut.";

        // Format số điện thoại chuẩn quốc tế (+84...) cho SMS Gateway
        $formattedPhone = $phone;
        if (str_starts_with($phone, '0')) {
            $formattedPhone = '+84' . substr($phone, 1);
        }

        // Always log OTP code to system log for easy debugging/testing
        Log::info("=== OTP CODE GENERATED FOR PHONE {$phone} ({$formattedPhone}) ===: {$otp}");

        // 1. NẾU CÓ CẤU HÌNH TEXTBEE API (TextBee.dev - SMS Gateway)
        $textBeeKey = env('TEXTBEE_API_KEY');
        $textBeeDeviceId = env('TEXTBEE_DEVICE_ID');

        if ($textBeeKey) {
            try {
                $endpoint = $textBeeDeviceId 
                    ? "https://api.textbee.dev/api/v1/gateway/devices/{$textBeeDeviceId}/send-sms"
                    : "https://api.textbee.dev/api/v1/gateway/send-sms";

                $response = Http::withHeaders([
                    'x-api-key' => $textBeeKey,
                    'Content-Type' => 'application/json'
                ])->post($endpoint, [
                    'recipients' => [$formattedPhone],
                    'message' => $message,
                ]);

                Log::info("TextBee SMS Response: " . $response->body());
                if ($response->successful()) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::error("TextBee SMS Send Error: " . $e->getMessage());
            }
        }

        // 2. NẾU CÓ CẤU HÌNH TWILIO API
        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_AUTH_TOKEN') ?: env('TWILIO_API_SECRET');
        $twilioNumber = env('TWILIO_NUMBER');

        if ($twilioSid && $twilioToken && $twilioNumber) {
            try {
                $response = Http::withBasicAuth($twilioSid, $twilioToken)
                    ->asForm()
                    ->post("https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json", [
                        'From' => $twilioNumber,
                        'To' => $formattedPhone,
                        'Body' => $message,
                    ]);

                Log::info("Twilio Response: " . $response->body());
                return $response->successful();
            } catch (\Exception $e) {
                Log::error("Twilio Send Error: " . $e->getMessage());
            }
        }

        return true;
    }
}
