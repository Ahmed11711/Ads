<?php

namespace App\Http\Controllers\Admin\notifications;

use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use App\Http\Requests\Admin\notifications\notificationsUpdateRequest;
use App\Http\Resources\Admin\notifications\notificationsResource;
use App\Repositories\notifications\notificationsRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class notificationsController extends BaseController
{
 public function __construct(notificationsRepositoryInterface $repository)
 {
  parent::__construct();

  $this->initService(
   repository: $repository,
   collectionName: 'notifications'
  );

  $this->storeRequestClass = notificationsStoreRequest::class;
  $this->updateRequestClass = notificationsUpdateRequest::class;
  $this->resourceClass = notificationsResource::class;
 }

 public function store(Request $request): JsonResponse
 {
  // 1️⃣ استرجاع البيانات validated
  $validated = app($this->storeRequestClass)->validated();

  try {
   DB::beginTransaction();

   // 2️⃣ نحدد مين هيستقبل الإشعار
   $emails = $validated['emails'] ?? null; // array من ايميلات لو موجودة

   if ($emails && is_array($emails) && count($emails) > 0) {
    // لو في ايميلات محددة، نضيف notifications لكل ايميل
    foreach ($emails as $email) {
     $this->repository->create(array_merge($validated, ['email' => $email]));
    }
   } else {
    // لو ما فيش ايميلات محددة، نضيف لكل الـ users
    $users = DB::table('users')->pluck('email'); // جلب كل الايميلات
    foreach ($users as $email) {
     $this->repository->create(array_merge($validated, ['email' => $email]));
    }
   }

   DB::commit();

   return $this->successResponse([], 'Notifications created successfully', 201);
  } catch (\Throwable $e) {
   DB::rollBack();
   Log::error("Error creating notifications: " . $e->getMessage());
   return $this->errorResponse('Failed to create notifications', 500);
  }
 }
}
