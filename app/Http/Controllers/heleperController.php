<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class heleperController extends Controller
{

 public function notification(notificationsStoreRequest $request)
 {
  $data = $request->validated();

  // تحويل emails من string إلى array لو لزم
  if (isset($data['emails']) && is_string($data['emails'])) {
   $emails = trim($data['emails'], '[]');
   $data['emails'] = array_map('trim', explode(',', $emails));
  }

  $emails = $data['emails'] ?? [];

  // جلب الـ users من قاعدة البيانات اللي ايميلاتهم موجودة في الـ emails المرسلة
  $users = DB::table('users')
   ->whereIn('email', $emails)
   ->get(['id', 'email']); // نجيب id و email

  $userIds = $users->pluck('id')->toArray();

  // دلوقتي تقدر تستخدم $userIds لتخزين notification لكل user
  foreach ($userIds as $userId) {
   // مثال: تخزين notification
   DB::table('notifications')->insert([
    'user_id' => $userId,
    'title' => $data['title'],
    'message' => $data['message'],
    'is_read' => $data['is_read'] ?? 0,
    'created_at' => now(),
    'updated_at' => now(),
   ]);
  }

  return response()->json([
   'success' => true,
   'message' => 'Notifications sent successfully',
   'user_ids' => $userIds,
  ]);
 }
}
