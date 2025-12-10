<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\User\UserStoreRequest;

use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\Admin\User\UserResource;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
 public function __construct(UserRepositoryInterface $repository)
 {
  parent::__construct();

  $this->initService(
   repository: $repository,
   collectionName: 'User'
  );

  $this->storeRequestClass = UserStoreRequest::class;
  $this->updateRequestClass = UserUpdateRequest::class;
  $this->resourceClass = UserResource::class;
 }

 public function update(Request $request, int $id): JsonResponse
 {
  // 1️⃣ تحديث البيانات الأساسية عن طريق BaseController
  $response = parent::update($request, $id);

  // 2️⃣ نجيب الرصيد اللي اتبعت
  $data = $request->only(['balance', 'affiliate_balance']);

  if (!empty(array_filter($data, fn($v) => $v !== null))) {
   // تحديث أو إنشاء record في جدول user_balances
   $balanceRecord = DB::table('user_balances')->where('user_id', $id)->first();

   if ($balanceRecord) {
    DB::table('user_balances')->where('user_id', $id)->update($data);
   } else {
    DB::table('user_balances')->insert(array_merge(['user_id' => $id], $data));
   }
  }

  // 3️⃣ نجيب البيانات النهائية للـ user
  $user = $this->repository->find($id);
  $balance = DB::table('user_balances')->where('user_id', $id)->first();

  // حول الـ user من array/object لو لزم الأمر
  $userArray = is_array($user) ? $user : $user->toArray();
  $balanceArray = (array) $balance;
  $finalData = (object) array_merge($userArray, ['balance' => $balanceArray]);

  return $this->successResponse(
   new $this->resourceClass($finalData),
   'Record updated successfully'
  );
 }
}
