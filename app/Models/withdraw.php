<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Withdraw extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // عند الإنشاء
        static::creating(function ($withdraw) {
            if (empty($withdraw->transaction_id)) {
                $withdraw->transaction_id = 'TXN-' . strtoupper(uniqid());
            }
        });

        // عند التعديل
        static::updating(function ($withdraw) {

            // اتغيرت الحالة لـ reject
            if (
                $withdraw->isDirty('status') &&
                $withdraw->getOriginal('status') !== 'reject' &&
                $withdraw->status === 'reject'
            ) {

                DB::transaction(function () use ($withdraw) {

                    $userBalance = userBalance::where('user_id', $withdraw->user_id)->lockForUpdate()->first();

                    if (!$userBalance) {
                        return;
                    }

                    if ($withdraw->type === 'affiliate') {
                        $userBalance->affiliate_balance += $withdraw->amount;
                    } else {
                        $userBalance->balance += $withdraw->amount;
                    }

                    $userBalance->save();
                });
            }
        });
    }
}
