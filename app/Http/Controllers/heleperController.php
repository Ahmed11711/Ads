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

  if (!empty($emails)) {
   // إرسال للإيميلات المحددة
   $users = DB::table('users')
    ->whereIn('email', $emails)
    ->get(['id', 'email']);

   $userIds = $users->pluck('id')->toArray();

   foreach ($userIds as $userId) {
    DB::table('notifications')->insert([
     'user_id' => $userId,
     'title' => $data['title'],
     'message' => $data['message'],
     'is_read' => $data['is_read'] ?? 0,
     'type' => 'individual',
     'created_at' => now(),
     'updated_at' => now(),
    ]);
   }
  } else {
   // إرسال للجميع → record واحد
   DB::table('notifications')->insert([
    'user_id' => 0, // أو أي قيمة dummy لتمثيل "الكل"
    'title' => $data['title'],
    'message' => $data['message'],
    'is_read' => 0,
    'type' => 'all', // كل المستخدمين يقدروا يشوفوه
    'created_at' => now(),
    'updated_at' => now(),
   ]);
  }

  return response()->json([
   'success' => true,
   'message' => 'Notifications sent successfully',
  ]);
 }
}
