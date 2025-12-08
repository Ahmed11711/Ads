<?php

namespace App\Http\Controllers\Api\OfferwallIntegrations;

use App\DTOs\AdGem\AdGemWebhookDto;
use App\DTOs\AdGem\GenerateOfferLinkDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\AdGem\AdGemGenerateOfferLinkRequest;
use App\Http\Requests\Integrations\AdGem\AdGemWebhookRequest;
use App\Services\AdGemService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdGemController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AdGemService $adGemService
    ) {
        // code...
    }

    public function triggerProcess(Request $request): JsonResponse
    {
        $response = $this->adGemService->getOffers($request->validated());

        return $this->respondDto($response);
    }

    public function generateOfferLink(AdGemGenerateOfferLinkRequest $request): JsonResponse
    {
        $dto = GenerateOfferLinkDto::fromArray($request->validated());

        $response = $this->adGemService->generateOfferLink($dto);

        return $this->successResponse(
            ['link' => $response],
            'Offer link generated successfully'
        );
    }

    public function onOfferComplete(AdGemWebhookRequest $request): JsonResponse
    {
        $dto = AdGemWebhookDto::fromRequest($request->validated());

        $response = $this->adGemService->onOfferComplete($dto);

        return $this->respondDto($response);
    }
}
