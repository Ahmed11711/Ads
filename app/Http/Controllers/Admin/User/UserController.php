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
  $response = parent::update($request, $id);

  $data = $request->only(['balance', 'affiliate_balance']);

  if (!empty(array_filter($data, fn($v) => $v !== null))) {

   $balanceRecord = DB::table('user_balances')->where('user_id', $id)->first();

   if ($balanceRecord) {
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
    $insertData = ['user_id' => $id];

    if (array_key_exists('balance', $data)) {
     $insertData['balance'] = $data['balance'];
    }
    if (array_key_exists('affiliate_balance', $data)) {
     $insertData['affiliate_balance'] = $data['affiliate_balance'];
    }

    if (count($insertData) > 1) {
     DB::table('user_balances')->insert($insertData);
    }
   }

   // 👇 خصم الفرق فقط من setting
   $diff = ($data['balance'] ?? 0) + ($data['affiliate_balance'] ?? 0);

   if ($diff != 0) {
    DB::table('settings')
     ->where('key', 'avalible-riget')
     ->decrement('value', $diff);
   }
  }

  $user = $this->repository->find($id);
  $user->balance = DB::table('user_balances')->where('user_id', $id)->first();

  return $this->successResponse(
   new $this->resourceClass($user),
   'Record updated successfully'
  );
 }
}
