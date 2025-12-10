<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Models\notifications;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
 use ApiResponseTrait;
 public function index(Request $request)
 {
  $user = auth()->user();
  $notifications = notifications::where('user_id', $user->id)->paginate(10);
  return $this->successResponsePaginate($notifications, 'Notifications retrieved successfully.');
 }
}
