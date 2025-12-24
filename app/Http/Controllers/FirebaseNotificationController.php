<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Models\User;

class FirebaseNotificationController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('firebase/firebase_credentials.json'));

        $this->messaging = $factory->createMessaging();
    }

    public function sendPush(Request $request)
    {
        $user = User::select('fcm_token')->where('email', 'ahmedsamir11711@gmail.com')->first();
        $fcmToken = $user?->fcm_token;

        if (!$fcmToken) {
            return response()->json([
                'message' => 'FCM token not found for this user'
            ], 404);
        }

        $title = "titl";
        $body = "title";

        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(Notification::create($title, $body))
            ->withData(['extra' => 'value']);

        try {
            $this->messaging->send($message);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Notification sent successfully'
        ]);
    }
}
