<?php

namespace App\Repositories\userWithAds;

use App\Repositories\userWithAds\userWithAdsRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\userWithAds;

class userWithAdsRepository extends BaseRepository implements userWithAdsRepositoryInterface
{
    public function __construct(userWithAds $model)
    {
        parent::__construct($model);
    }
}
