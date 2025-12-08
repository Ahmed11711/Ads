<?php

namespace App\Http\Controllers\Api\OfferwallIntegrations;

use App\DTOs\CPX\CpxDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\CpxTriggerRequest;
use App\Services\CPXService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class CPXServeyController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected CPXService $cpxService
    ) {}

    public function triggerProcess(CpxTriggerRequest $request): JsonResponse
    {
        $dto = CpxDto::fromRequest($request->validated());

        $response = $this->cpxService->getOfferWall($dto, $request->except('ext_user_id', 'ip_user'));

        return $this->respondDto($response);
    }
}
