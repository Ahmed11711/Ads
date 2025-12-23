<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PushMoneyForUser;
use App\Models\User;
use App\Models\UserBalance;
use App\Traits\BalanceSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PushMoneyForUserController extends Controller
{
    use BalanceSystem;
    public function receiveMoney(PushMoneyForUser $request)
    {

        $amount = $request->amount;

        return DB::transaction(function () use ($amount) {

            $usersCount = User::count();

            if ($usersCount == 0) {
                return response()->json([
                    'message' => 'No users found'
                ], 400);
            }

            $share = round($amount / $usersCount, 2);

            $users = User::all();

            foreach ($users as $user) {

                UserBalance::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'balance' => DB::raw("COALESCE(balance,0) + {$share}")
                    ]
                );
            }

            $this->discountFromAvilable($amount);

            return response()->json([
                'message' => 'Money distributed successfully',
                'total_amount' => $amount,
                'users_count' => $usersCount,
                'share_per_user' => $share
            ]);
        });
    }
}
