<?php

namespace App\Http\Controllers\Admin\withdraw;

use App\Repositories\withdraw\withdrawRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\withdraw\withdrawStoreRequest;
use App\Http\Requests\Admin\withdraw\withdrawUpdateRequest;
use App\Http\Resources\Admin\withdraw\withdrawResource;

class withdrawController extends BaseController
{
 public function __construct(withdrawRepositoryInterface $repository)
 {
  parent::__construct();

  $this->initService(
   repository: $repository,
   collectionName: 'withdraw'
  );

  $this->storeRequestClass = withdrawStoreRequest::class;
  $this->updateRequestClass = withdrawUpdateRequest::class;
  $this->resourceClass = withdrawResource::class;
 }
}
