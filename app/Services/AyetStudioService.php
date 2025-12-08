<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AyetStudio\AyetStudioDto;
use App\DTOs\AyetStudio\AyetStudioWebhookDto;
use App\DTOs\AyetStudio\GenerateOfferLinkDto;
use App\DTOs\ResponseDto\ServiceResponseDto;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AyetStudioService
{
    protected ?string $appKey;

    protected ?string $adslot;

    protected ?string $baseUrl;

    public function __construct()
    {
        $this->appKey = config('services.ayet_studios.app_key');
        $this->baseUrl = config('services.ayet_studios.url', 'https://www.ayetstudios.com');
        $this->adslot = config('services.ayet_studios.adslot');
    }

    /**
     * Retrieve offers matching the provided parameters
     */
    public function getOfferWall(AyetStudioDto $dto, ?array $options = []): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/offers/offerwall_api/$dto->adSlot";

        $queryParams = [
            'external_identifier' => $dto->externalIdentifier,
            ...$options,
        ];

        try {
            $response = Http::timeout(5)->get($url, $queryParams);
        } catch (\Throwable $th) {
            return ServiceResponseDto::error('Failed to fetch offers');
        }

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch offers');
        }

        return ServiceResponseDto::success()
            ->setMessage('Offers fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }

    public function generateOfferLink(GenerateOfferLinkDto $dto, array $extraParams = []): string
    {
        $queryParam = [
            'external_identifier' => $dto->userId,
            'offer_id' => $dto->offerId,
            'adslot' => $this->adslot,
            'app_key' => $this->appKey,
            ...$extraParams,
        ];

        $payload = implode('|', [
            $queryParam['external_identifier'],
            $queryParam['offer_id'],
            $queryParam['adslot'],
            $queryParam['app_key'],
        ]);

        $queryParam['hash'] = hash_hmac(
            'sha256', $payload,
            config('services.ayet_studios.identifier')
        );

        return "{$this->baseUrl}/offers/offerwall_api?".http_build_query($queryParam);
    }

    public function onOfferComplete(AyetStudioWebhookDto $dto): ServiceResponseDto
    {
        $expectedHash = hash_hmac('sha256', $dto->transId, config('services.ayet_studios.identifier'));

        if (! $dto->hash || $dto->hash != $expectedHash) {
            return ServiceResponseDto::error('Invalid hash', 200);
        }

        Log::info('AyetStudio Webhook received', [
            'status' => $dto->status,
            'transId' => $dto->transId,
            'userId' => $dto->userId,
            'amountUsd' => $dto->amountUsd,
            'offerId' => $dto->offerId,
            'hash' => $dto->hash,
        ]);

        return ServiceResponseDto::success()
            ->setMessage('ok')
            ->setStatus(200);
    }

    public function getOffers(int $placementId, array $options = []): ServiceResponseDto
    {
        if (! $this->appKey) {
            return ServiceResponseDto::error('Failed to fetch offers, Please try again later', 500);
        }

        $url = "{$this->baseUrl}/offers/get/{$placementId}";

        $queryParams = ['apiKey' => $this->appKey, ...$options];

        try {
            $response = Http::timeout(5)->get($url, $queryParams);
        } catch (\Throwable $th) {
            return ServiceResponseDto::error('Failed to fetch offers', 500);
        }

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch offers', 500);
        }

        return ServiceResponseDto::success()
            ->setMessage('Offers fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }

    public function getAccountDetails(): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/api2/account/details";

        try {
            $response = Http::timeout(5)->get($url, [
                'apiKey' => $this->appKey,
            ]);
        } catch (\Throwable $th) {
            return ServiceResponseDto::error('Failed to fetch account details', 500);
        }

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch account details', 500);
        }

        return ServiceResponseDto::success()
            ->setMessage('Account details fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }
}
