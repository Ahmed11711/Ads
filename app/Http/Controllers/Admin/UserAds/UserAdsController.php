<?php

namespace App\Http\Controllers\Admin\userAds;

use App\Repositories\userAds\userAdsRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\userAds\userAdsStoreRequest;
use App\Http\Requests\Admin\userAds\userAdsUpdateRequest;
use App\Http\Resources\Admin\userAds\userAdsResource;

class userAdsController extends BaseController
{
    public function __construct(userAdsRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'userAds'
        );

        $this->storeRequestClass = userAdsStoreRequest::class;
        $this->updateRequestClass = userAdsUpdateRequest::class;
        $this->resourceClass = userAdsResource::class;
    }
}
