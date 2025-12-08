<?php

namespace App\Repositories\Company;

use App\Repositories\Company\CompanyRepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Company;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }
}
