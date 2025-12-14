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
use Illuminate\Support\Facades\DB;

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

   $user = User::findOrFail($id);

   // تحديث بيانات اليوزر
   $user->update($request->except(['balance', 'affiliate_balance']));

   if ($request->hasAny(['balance', 'affiliate_balance'])) {

    $balance = $request->balance ?? 0;
    $affiliateBalance = $request->affiliate_balance ?? 0;

    $totalAmount = $balance + $affiliateBalance;

    if ($totalAmount <= 0) {
     throw new \Exception('قيمة الرصيد غير صحيحة');
    }

    $setting = Setting::where('key', 'avalible-riget')
     ->lockForUpdate()
     ->firstOrFail();

    if ($setting->value < $totalAmount) {
     throw new \Exception('الرصيد العام غير كافي');
    }

    // تحديث رصيد المستخدم
    UserBalance::updateOrCreate(
     ['user_id' => $user->id],
     [
      'balance' => $balance,
      'affiliate_balance' => $affiliateBalance,
     ]
    );

    // خصم مباشر من رصيد السيستم
    $setting->decrement('value', $totalAmount);
   }
  });

  return $this->successResponse(true, 'User updated & balance deducted');
 }
}
