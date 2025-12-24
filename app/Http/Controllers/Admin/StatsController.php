<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Company;
use App\Models\RigetSupply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\userBalance;
use App\Models\withdraw;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function getStats()
    {
        $total_users = User::count();


        $today = Carbon::today(); // تاريخ اليوم بدون وقت

        $active_today = User::whereDate('last_login_at', $today)->count();

        $total_revenue = Company::sum('amount');

        $pending_withdrawals = withdraw::where('status', 'pending')->count();
        $confirmedWithdraw = withdraw::where('status', 'confirmed')->sum('amount');

        $company_revenues = Company::select('name', 'amount')->get();
        $totalMoney = userBalance::sum('balance') + userBalance::sum('affiliate_balance');

        return response()->json([
            'total_users' => $total_users,
            'total_user_active' => $active_today,
            'total_revenue' => $total_revenue,
            'pending_withdrawals' => $pending_withdrawals,
            'confirmed_withdraw' => $confirmedWithdraw,
            'company_revenues' => $company_revenues,
            'total_money_users' => $totalMoney,
        ]);
    }
}
