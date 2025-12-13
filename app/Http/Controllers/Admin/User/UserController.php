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
  // 1️⃣ تنفيذ التحديثات العادية عبر الريبو
  $response = parent::update($request, $id);

  // 2️⃣ جلب البيانات المطلوبة للتعديل
  $data = $request->only(['balance', 'affiliate_balance']);

  // 3️⃣ التحقق من وجود أي قيمة للتحديث
  if (!empty(array_filter($data, fn($v) => $v !== null))) {

   // جلب سجل المستخدم من جدول user_balances
   $balanceRecord = DB::table('user_balances')->where('user_id', $id)->first();

   if ($balanceRecord) {
    // لو السجل موجود → تحديث القيم الموجودة فقط
    $updateData = [];

    if (array_key_exists('balance', $data)) {
     $updateData['balance'] = $balanceRecord->balance + $data['balance'];
    }
    if (array_key_exists('affiliate_balance', $data)) {
     $updateData['affiliate_balance'] = $balanceRecord->affiliate_balance + $data['affiliate_balance'];
    }

    if (!empty($updateData)) {
     DB::table('user_balances')->where('user_id', $id)->update($updateData);
    }
   } else {
    // لو السجل غير موجود → إدخال فقط القيم الموجودة في الـ request
    $insertData = ['user_id' => $id];

    if (array_key_exists('balance', $data)) {
     $insertData['balance'] = $data['balance'];
    }
    if (array_key_exists('affiliate_balance', $data)) {
     $insertData['affiliate_balance'] = $data['affiliate_balance'];
    }

    // لو فيه قيم للتخزين → insert
    if (count($insertData) > 1) {
     DB::table('user_balances')->insert($insertData);
    }
   }
  }

  // 4️⃣ استرجاع البيانات النهائية
  $user = $this->repository->find($id);
  $balance = DB::table('user_balances')->where('user_id', $id)->first();
  $user->balance = $balance;

  return $this->successResponse(
   new $this->resourceClass($user),
   'Record updated successfully'
  );
 }
}
