<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

    public function notification(notificationsStoreRequest $request)
    {
        $data = $request->validated();

        // تحويل الإيميلات إلى array
        $emails = [];
        if (isset($data['emails'])) {
            if (is_string($data['emails'])) {
                $emailsStr = trim($data['emails'], '[]');
                $emails = array_filter(array_map('trim', explode(',', $emailsStr)));
            } elseif (is_array($data['emails'])) {
                $emails = $data['emails'];
            }
        }

        if (!empty($emails)) {
            // إرسال للإيميلات المحددة فقط
            $users = User::whereIn('email', $emails)->get(['id', 'email', 'fcm_token']);

            foreach ($users as $user) {
                // تسجيل Notification في DB
                DB::table('notifications')->insert([
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'is_read' => $data['is_read'] ?? 0,
                    'type' => 'individual',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // إرسال Push Notification عبر Firebase
                if ($user->fcm_token) {
                    $message = CloudMessage::withTarget('token', $user->fcm_token)
                        ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                        ->withData(['type' => 'individual']);
                    try {
                        $this->messaging->send($message);
                    } catch (\Exception $e) {
                        // ممكن تعمل logging بدل ما توقف العملية
                        Log::error("FCM failed for user {$user->id}: " . $e->getMessage());
                    }
                }
            }
        } else {
            // إرسال Notification للجميع
            DB::table('notifications')->insert([
                'user_id' => 0,
                'title' => $data['title'],
                'message' => $data['message'],
                'is_read' => 0,
                'type' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إرسال Push Notification لكل المستخدمين اللي عندهم fcm_token
            $users = User::whereNotNull('fcm_token')->get(['id', 'fcm_token']);
            foreach ($users as $user) {
                $message = CloudMessage::withTarget('token', $user->fcm_token)
                    ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                    ->withData(['type' => 'all']);
                try {
                    $this->messaging->send($message);
                } catch (\Exception $e) {
                    Log::error("FCM failed for user {$user->id}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications sent successfully',
        ]);
    }
}
