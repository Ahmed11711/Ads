<?php

namespace App\Traits;

use App\Models\setting;
use App\Models\User;
use App\Models\UserBalance;

trait BalanceSystem
{

    public function addForAffiliate()
    {
        $setting = setting::where('key', 'avalible-riget')
            ->lockForUpdate()
            ->firstOrFail();

        $setting->decrement('value', 1);
    }

    public function discountFromAvilable($amount)
    {
        $setting = setting::where('key', 'avalible-riget')
            ->lockForUpdate()
            ->firstOrFail();
        $setting->decrement('value', $amount);
    }

    public function addAffiliateForParent($referred_by)
    {
        // 1️⃣ جلب المستخدم اللي استخدم الكود
        $user = User::where('affilte_code', $referred_by)->first();

        if (!$user) {
            return false; // أو throw exception حسب احتياجك
        }

        // 2️⃣ جلب قيمة العمولة من الإعدادات
        $setting = Setting::where('key', 'affiliate_profit')
            ->lockForUpdate()
            ->firstOrFail();

        $amount = (float) $setting->value;

        // 3️⃣ تحديث أو إنشاء رصيد المستخدم
        $userBalance = UserBalance::updateOrCreate(
            ['user_id' => $user->id],
            ['balance' => DB::raw("COALESCE(balance,0) + {$amount}")]
        );

        return $userBalance;
    }
}
