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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

 public function index(Request $request): JsonResponse
 {
  return 55555;
  try {
   // 1️⃣ جايب الـ query من الريبو
   return  $query = $this->repository->query()->with('balance'); // هنا عملنا eager load للعلاقة

   // 2️⃣ البحث على أي حقل
   if ($search = $request->input('search')) {
    $query->where(function ($q) use ($search) {
     $table = $q->getModel()->getTable();
     $stringColumns = Schema::getColumnListing($table);
     $stringColumns = array_filter($stringColumns, function ($col) {
      return !in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at']);
     });
     foreach ($stringColumns as $column) {
      $q->orWhere($column, 'like', "%{$search}%");
     }
    });
   }

   // 3️⃣ أي فلترة إضافية
   $excluded = ['search', 'page', 'per_page'];
   foreach ($request->except($excluded) as $key => $value) {
    if ($value === null || $value === '') continue;
    if (Schema::hasColumn($query->getModel()->getTable(), $key)) {
     $query->where($key, $value);
    }
   }

   // 4️⃣ Pagination
   $perPage = $request->input('per_page', 10);
   $data = $query->latest()->paginate($perPage);

   // 5️⃣ Resource مع الـ balances موجودة مسبقًا
   $data = $this->resourceClass::collection($data);

   return $this->successResponsePaginate($data, "{$this->collectionName} list retrieved successfully");
  } catch (\Throwable $e) {
   Log::error("Error in {$this->collectionName} index: " . $e->getMessage());
   return $this->errorResponse("Failed to fetch data", 500);
  }
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
