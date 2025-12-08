<?php

namespace App\Http\Controllers\Api\OfferwallIntegrations;

use App\DTOs\AyetStudio\AyetStudioDto;
use App\DTOs\AyetStudio\AyetStudioWebhookDto;
use App\DTOs\AyetStudio\GenerateOfferLinkDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\AyetStudio\AyetGenerateOfferLinkRequest;
use App\Http\Requests\Integrations\AyetStudio\AyetTriggerRequest;
use App\Http\Requests\Integrations\AyetStudio\AyetWebhookRequest;
use App\Services\AyetStudioService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class AyetStudioController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AyetStudioService $ayetStudioService
    ) {}

    public function triggerProcess(AyetTriggerRequest $request): JsonResponse
    {
        $dto = AyetStudioDto::fromRequest($request->validated());

        $response = $this->ayetStudioService->getOfferWall($dto, $request->except('adSlot', 'external_identifier'));

        return $this->respondDto($response);
    }

    public function generateOfferLink(AyetGenerateOfferLinkRequest $request): JsonResponse
    {
        $dto = GenerateOfferLinkDto::fromRequest($request->validated());

        $response = $this->ayetStudioService->generateOfferLink($dto);

        return $this->successResponse(
            ['link' => $response],
            'Offer link generated successfully'
        );
    }

    public function onOfferComplete(AyetWebhookRequest $request): JsonResponse
    {
        $dto = AyetStudioWebhookDto::fromRequest($request->validated());

        $response = $this->ayetStudioService->onOfferComplete($dto);

        return $this->respondDto($response);
    }
}
