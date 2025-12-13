<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Models\User;
use App\Models\userBalance;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UpdateUserController extends Controller
{
 use ApiResponseTrait;


 public function index()
 {
  return $user = User::get();
 }
 public function update(UserUpdateRequest $request, $id)
 {

  $userData = $request->except(['balance', 'affiliate_balance']); // نستبعد الـ balances
  $user = User::findOrFail($id);
  $user->update($userData);
  if ($request->hasAny(['balance', 'affiliate_balance'])) {
   $balanceData = [
    'balance' => $request->balance ?? 0,
    'affiliate_balance' => $request->affiliate_balance ?? 0,
   ];

   userBalance::updateOrCreate(
    ['user_id' => $user->id],
    $balanceData
   );
  }
  return $this->successResponse($user, "updated");
 }
}
