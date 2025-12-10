<?php

namespace App\Repositories\UserAds;

use App\Repositories\UserAds\UserAdsRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\UserAds;

class UserAdsRepository extends BaseRepository implements UserAdsRepositoryInterface
{
    public function __construct(UserAds $model)
    {
        parent::__construct($model);
    }
}
