<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Repositories\User\UserRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Http\Resources\Admin\User\UserResource;

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
  $user = $this->repository->find($id);

  if ($user && !empty(array_filter($data, fn($v) => $v !== null))) {
   $user->balance()->updateOrCreate(
    ['user_id' => $user->id],
    $data
   );
  }

  return $this->successResponse(
   new $this->resourceClass($user->load('balance')),
   'Record updated successfully'
  );
 }
}
