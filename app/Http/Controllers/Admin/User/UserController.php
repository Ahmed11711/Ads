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
  // تحديث البيانات الأساسية عن طريق BaseController
  $response = parent::update($request, $id);

  // تحديث أو إنشاء record في جدول user_balances
  $data = $request->only(['balance', 'affiliate_balance']);

  if (!empty(array_filter($data, fn($v) => $v !== null))) {
   $balanceRecord = DB::table('user_balances')->where('user_id', $id)->first();

   if ($balanceRecord) {
    DB::table('user_balances')->where('user_id', $id)->update($data);
   } else {
    DB::table('user_balances')->insert(array_merge(['user_id' => $id], $data));
   }
  }

  // استرجاع اليوزر الأصلي
  $user = $this->repository->find($id);

  // استرجاع الـ balance
  $balance = DB::table('user_balances')->where('user_id', $id)->first();

  // تحويل الـ balance لكائن stdClass مؤقت
  $user->balance = $balance;

  return $this->successResponse(
   new $this->resourceClass($user),
   'Record updated successfully'
  );
 }
}
