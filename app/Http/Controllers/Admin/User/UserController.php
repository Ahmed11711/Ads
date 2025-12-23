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

  $this->storeRequestClass  = UserStoreRequest::class;
  $this->updateRequestClass = UserUpdateRequest::class;
  $this->resourceClass      = UserResource::class;
 }

 public function update(Request $request, int $id): JsonResponse
 {
  DB::beginTransaction();

  try {
   // 1️⃣ تحديث بيانات الـ User الأساسية عن طريق BaseController
   $response = parent::update($request, $id);

   // 2️⃣ الحصول على الـ balances من الـ request
   $data = $request->only(['balance', 'affiliate_balance']);

   if (!empty(array_filter($data, fn($v) => $v !== null))) {

    // 3️⃣ جلب record الـ balance الخاص باليوزر
    $balanceRecord = \App\Models\UserBalance::firstOrNew(['user_id' => $id]);

    // 4️⃣ تحديث أو إدخال القيم الجديدة
    if (array_key_exists('balance', $data)) {
     $balanceRecord->balance = $data['balance'];
    }
    if (array_key_exists('affiliate_balance', $data)) {
     $balanceRecord->affiliate_balance = $data['affiliate_balance'];
    }
    $balanceRecord->save();

    // 5️⃣ خصم نفس القيمة من setting
    $diff = ($data['balance'] ?? 0) + ($data['affiliate_balance'] ?? 0);
    if ($diff != 0) {
     DB::table('settings')
      ->where('key', 'avalible-riget')
      ->decrement('value', $diff);
    }
   }

   // 6️⃣ جلب الـ user مع الـ balance relation
   $user = $this->repository->find($id);
   $user->load('balanceRecord');

   // 7️⃣ تعديل الـ attributes عشان Resource يشتغل بدون null
   $user->balance = $user->balanceRecord?->balance ?? 0;
   $user->affiliate_balance_value = $user->balanceRecord?->affiliate_balance ?? 0;

   DB::commit();

   return $this->successResponse(
    new $this->resourceClass($user),
    'Record updated successfully'
   );
  } catch (\Exception $e) {
   DB::rollBack();
   return $this->errorResponse($e->getMessage(), 500);
  }
 }
}
