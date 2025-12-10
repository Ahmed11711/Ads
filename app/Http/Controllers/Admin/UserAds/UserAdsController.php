<?php

namespace App\Http\Controllers\Admin\UserAds;

use App\Repositories\UserAds\UserAdsRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\UserAds\UserAdsStoreRequest;
use App\Http\Requests\Admin\UserAds\UserAdsUpdateRequest;
use App\Http\Resources\Admin\UserAds\UserAdsResource;

class UserAdsController extends BaseController
{
    public function __construct(UserAdsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'UserAds'
        );

        $this->storeRequestClass = UserAdsStoreRequest::class;
        $this->updateRequestClass = UserAdsUpdateRequest::class;
        $this->resourceClass = UserAdsResource::class;
    }
}
