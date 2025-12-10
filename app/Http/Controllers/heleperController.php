<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use Illuminate\Http\Request;

class heleperController extends Controller
{
 public function notification(notificationsStoreRequest $request)
 {
  $data = $request->validated();

  // تحويل emails من string مفصول بفواصل إلى array
  if (isset($data['emails']) && is_string($data['emails'])) {
   // إزالة الأقواس المربعة لو موجودة
   $emails = trim($data['emails'], '[]');
   // تقسيم على الفاصلة وتنظيف الفراغات
   $emailsArray = array_map('trim', explode(',', $emails));
   $data['emails'] = $emailsArray;
  }

  // لو emails أصلاً array، يبقى يفضل كما هو
  // الآن $data['emails'] جاهز كـ array
  return $data;
 }
}
