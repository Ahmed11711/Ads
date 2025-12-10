<?php

namespace App\Http\Controllers\Admin\userWithAds;

use App\Repositories\userWithAds\userWithAdsRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\userWithAds\userWithAdsStoreRequest;
use App\Http\Requests\Admin\userWithAds\userWithAdsUpdateRequest;
use App\Http\Resources\Admin\userWithAds\userWithAdsResource;

class userWithAdsController extends BaseController
{
    public function __construct(userWithAdsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'userWithAds'
        );

        $this->storeRequestClass = userWithAdsStoreRequest::class;
        $this->updateRequestClass = userWithAdsUpdateRequest::class;
        $this->resourceClass = userWithAdsResource::class;
    }
}
