<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotficatonRequest;
use App\Models\notifications;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
 use ApiResponseTrait;
 public function send(NotficatonRequest $request)
 {
  $user = auth()->user();
  $data = $request->validated();

  $client = User::where('email', $data['email'])->first();

  if (!$client) {
   return response()->json(['success' => false, 'message' => 'User not found'], 404);
  }

  $notification = notifications::create([
   'user_id' => $client->id,
   'title' => "Notification from {$user->name}",
   'message' => 'Can you connect with me?',
  ]);

  return  $this->successResponse($notification, 'Notification sent successfully');
 }

 public function readAll(Request $request)
 {
  $user = auth()->user();

  notifications::where('user_id', $user->id)
   ->where('is_read', false) // اختياري
   ->update([
    'is_read' => true,
   ]);

  return response()->json([
   'status' => true,
   'message' => 'All notifications marked as seen',
  ]);
 }
}
