<?php

namespace App\Http\Controllers\Admin\Company;

use App\Repositories\Company\CompanyRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Company\CompanyStoreRequest;
use App\Http\Requests\Admin\Company\CompanyUpdateRequest;
use App\Http\Resources\Admin\Company\CompanyResource;

class CompanyController extends BaseController
{
    public function __construct(CompanyRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Company'
        );

        $this->storeRequestClass = CompanyStoreRequest::class;
        $this->updateRequestClass = CompanyUpdateRequest::class;
        $this->resourceClass = CompanyResource::class;
    }
}
