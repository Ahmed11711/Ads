<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpxServey extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'survey_id',
        'loi',
        'payout',
        'conversion_rate',
        'quality_score',
        'statistics_rating_count',
        'statistics_rating_avg',
        'type',
        'top',
        'details',
        'payout_publisher_usd',
        'href_new',
        'webcam',
    ];
}
