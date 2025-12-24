<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class heleperController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        // إعداد Firebase
        $factory = (new Factory)
            ->withServiceAccount(storage_path('firebase/firebase_credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * إرسال Push Notification
     */
    public function notification(notificationsStoreRequest $request)
    {
        $data = $request->validated();

        $fcmTokens = [];

        // تحديد المستلمين
        if (!empty($data['emails'])) {
            // لو الكلمة "all" موجودة أرسل لكل المستخدمين
            if ((is_array($data['emails']) && in_array('all', $data['emails'])) ||
                (is_string($data['emails']) && strtolower($data['emails']) === 'all')
            ) {
                $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
            } else {
                // تحويل الإيميلات إلى array لو كانت string
                $emails = is_array($data['emails']) ? $data['emails'] : explode(',', trim($data['emails'], '[]'));
                $fcmTokens = User::whereIn('email', $emails)
                    ->whereNotNull('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
            }
        } else {
            // لو مفيش أي إيميل محدد، نعتبر "all"
            $fcmTokens = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
        }

        // إرسال Push Notification لكل الـ tokens
        foreach ($fcmTokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                    ->withData(['type' => empty($data['emails']) ? 'all' : 'individual']);

                $this->messaging->send($message);
            } catch (\Exception $e) {
                Log::error("FCM failed for token {$token}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Push notifications sent successfully',
        ]);
    }
}
