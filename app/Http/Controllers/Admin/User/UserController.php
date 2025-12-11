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
  $response = parent::update($request, $id);

  $data = $request->only(['balance', 'affiliate_balance']);

  if (!empty(array_filter($data, fn($v) => $v !== null))) {
   $balanceRecord = DB::table('user_balances')->where('user_id', $id)->first();

   if ($balanceRecord) {
    // زيادة الرصيد الحالي بالقيم الجديدة
    $updateData = [];
    if (isset($data['balance'])) {
     $updateData['balance'] = $balanceRecord->balance + $data['balance'];
    }
    if (isset($data['affiliate_balance'])) {
     $updateData['affiliate_balance'] = $balanceRecord->affiliate_balance + $data['affiliate_balance'];
    }

    DB::table('user_balances')->where('user_id', $id)->update($updateData);
   } else {
    // لو مش موجود، نعمل إدخال جديد
    DB::table('user_balances')->insert(array_merge(['user_id' => $id], $data));
   }
  }

  // 3️⃣ استرجاع البيانات النهائية
  $user = $this->repository->find($id);
  $balance = DB::table('user_balances')->where('user_id', $id)->first();
  $user->balance = $balance;

  // 4️⃣ التأكد إن كل المسارات ترجع JsonResponse
  return $this->successResponse(
   new $this->resourceClass($user),
   'Record updated successfully'
  );
 }
}
