<?php

namespace App\Repositories\userAds;

use App\Repositories\userAds\userAdsRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\userAds;

class userAdsRepository extends BaseRepository implements userAdsRepositoryInterface
{
    public function __construct(userAds $model)
    {
        parent::__construct($model);
    }
}
