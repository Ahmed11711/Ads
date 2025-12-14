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
  DB::transaction(function () use ($request, $id) {

   $user = User::with('balance')->findOrFail($id);

   // تحديث بيانات اليوزر العادية
   $user->update($request->except(['balance', 'affiliate_balance']));

   if ($request->hasAny(['balance', 'affiliate_balance'])) {

    // الرصيد القديم
    $oldBalance = $user->balance?->balance ?? 0;
    $oldAffiliate = $user->balance?->affiliate_balance ?? 0;

    // الرصيد الجديد
    $newBalance = $request->balance ?? $oldBalance;
    $newAffiliate = $request->affiliate_balance ?? $oldAffiliate;

    // حساب الفرق
    $diff =
     ($newBalance - $oldBalance) +
     ($newAffiliate - $oldAffiliate);

    // تحديث رصيد المستخدم
    UserBalance::updateOrCreate(
     ['user_id' => $user->id],
     [
      'balance' => $newBalance,
      'affiliate_balance' => $newAffiliate,
     ]
    );

    // لو في فرق فعلي
    if ($diff != 0) {
     $this->adjustSettingByDiff($diff);
    }
   }
  });

  return $this->successResponse(true, 'User updated with balance difference');
 }

 public function adjustSettingByDiff($diff)
 {
  $setting = Setting::where('key', 'avalible-riget')
   ->lockForUpdate()
   ->firstOrFail();

  if ($diff > 0) {
   if ($setting->value < $diff) {
    throw new \Exception('Setting balance not enough');
   }

   $setting->decrement('value', $diff);
  }

  if ($diff < 0) {
   $setting->increment('value', abs($diff));
  }

  return $setting->fresh();
 }
}
