<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\notifications\notificationsStoreRequest;
use Illuminate\Http\Request;

class heleperController extends Controller
{
 public function notification(notificationsStoreRequest $request)
 {
  $data = $request->validated();
  return $data;
 }
}
