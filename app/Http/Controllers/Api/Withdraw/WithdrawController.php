<?php

namespace App\Http\Controllers\Api\Withdraw;

use App\Models\withdraw;
use App\Models\userBalance;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Withdraw\WithdrawRequest;
use App\Http\Requests\Api\Withdraw\AddBalanceRequest;

class WithdrawController extends Controller
{
 use ApiResponseTrait;

 public function index()
 {
  $user = auth()->user();

  // البيانات المصفحة
  $withdrawals = withdraw::where('user_id', $user->id)
   ->orderBy('created_at', 'desc')
   ->paginate(10);

  // إجمالي الفلوس المسحوبة المكتملة
  $totalWithdrawn = withdraw::where('user_id', $user->id)
   ->where('status', 'complete')  // شرط الاكتمال
   ->sum('amount');

  $data = [
   'withdrawals' => $withdrawals,
   'total_withdrawn' => $totalWithdrawn,

  ];
  return $this->successResponsePaginate($data, 'User withdrawals retrieved successfully');
 }


 public function withdraw(WithdrawRequest $request)
 {
  $user = auth()->user();
  $data = $request->validated();

  if ($data['type_withdraw'] === 'affiliate') {
   $balanceType = 'affiliate_balance';
  } else {
   $balanceType = 'balance';
  }


  $userBalance = userBalance::where('user_id', $user->id)->first();

  if (!$userBalance || $userBalance[$balanceType] < $data['amount']) {
   return $this->errorResponse('Insufficient balance for this withdrawal request.', 400);
  }
  // Deduct the amount from the user's balance
  $userBalance[$balanceType] -= $data['amount'];
  $userBalance->save();


  $data['user_id'] = $user->id;
  $withdraw = withdraw::create($data);
  $withdraw->status = 'pending';

  return $this->successResponse($withdraw, 'Withdrawal request submitted successfully.');
 }

 public function addBalance(AddBalanceRequest $request)
 {
  $user = auth()->user();
  $data = $request->validated();

  $userBalance = userBalance::firstOrCreate(
   ['user_id' => $user->id],
   ['affiliate_balance' => 0, 'balance' => 0]
  );

  $balanceType = $data['type_balance'];

  $userBalance->increment($balanceType, $data['amount']);
  return $this->successResponse($userBalance, 'Balance added successfully.');
 }
}
