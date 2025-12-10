<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Company;
use App\Models\RigetSupply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\withdraw;

class StatsController extends Controller
{
 public function getStats()
 {
  // إجمالي المستخدمين
  $total_users = User::count();

  // المستخدمين النشطين
  // $active_users = User::where('status', 'active')->count();

  // إجمالي الإيرادات من الشركات
  $total_revenue = Company::sum('amount');

  // السحوبات المعلقة
  $pending_withdrawals = withdraw::where('status', 'pending')->count();


  // تفاصيل السحوبات
  $withdrawal_methods = ['bank', 'bank_dollar', 'wallet'];
  $methods = [];
  foreach ($withdrawal_methods as $method) {
   $methods[$method] = [
    'count' => withdraw::where('method', $method)->count(),
    'volume' => withdraw::where('method', $method)->sum('amount'),
   ];
  }

  $statuses = ['approved', 'rejected'];
  $status_breakdown = [];
  foreach ($statuses as $status) {
   $status_breakdown[$status] = [
    'count' => withdraw::where('status', $status)->count(),
   ];
  }

  // إيرادات الشركات
  $company_revenues = Company::select('name', 'revenue')->get();

  return response()->json([
   'total_users' => $total_users,
   'total_revenue' => $total_revenue,
   'pending_withdrawals' => $pending_withdrawals,
   'withdrawal_breakdown' => [
    'methods' => $methods,
    'statuses' => $status_breakdown
   ],
   'company_revenues' => $company_revenues
  ]);
 }
}
