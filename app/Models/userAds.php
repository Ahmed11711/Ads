<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class userAds extends Model
{
    //

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

}