<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\Admin\User\UserResource;
use App\Models\setting;
use App\Models\User;
use App\Models\UserBalance;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UpdateUserController extends Controller
{
 use ApiResponseTrait;


 public function index()
 {
  $user = User::with('balance')->get();
  return $this->successResponse(UserResource::collection($user), 'sss');
 }
 public function update(UserUpdateRequest $request, $id)
 {
  $user = User::findOrFail($id);

  // تحديث بيانات المستخدم بدون الرصيد
  $userData = $request->except(['balance', 'affiliate_balance']);
  $user->update($userData);

  if ($request->hasAny(['balance', 'affiliate_balance'])) {

   // الرصيد القديم
   $oldBalance = UserBalance::where('user_id', $user->id)->first();

   $oldMainBalance = $oldBalance->balance ?? 0;
   $oldAffiliateBalance = $oldBalance->affiliate_balance ?? 0;

   // قيمة الخصم من الإعدادات
   $deduction = (float) Setting::where('key', 'avalible-riget')->value('value') ?? 0;

   // الرصيد الجديد
   $newBalance = $request->balance ?? $oldMainBalance;
   $newAffiliateBalance = $request->affiliate_balance ?? $oldAffiliateBalance;

   // لو حصل تعديل فعلي
   if (
    $newBalance != $oldMainBalance ||
    $newAffiliateBalance != $oldAffiliateBalance
   ) {
    $newBalance = max(0, $newBalance - $deduction);
   }

   UserBalance::updateOrCreate(
    ['user_id' => $user->id],
    [
     'balance' => $newBalance,
     'affiliate_balance' => $newAffiliateBalance,
    ]
   );
  }

  return $this->successResponse($user, "updated");
 }
}
