<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ResponseDto\ServiceResponseDto;
use Illuminate\Support\Facades\Http;

class AyetStudioService
{
    protected ?string $apiKey;

    protected ?string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.ayetstudios.api_key');
        $this->baseUrl = config('services.ayetstudios.url', 'https://www.ayetstudios.com');
    }

    /**
     * Retrieve offers matching the provided parameters
     */
    public function getOfferWall(string $adSlot, string $external_identifier, array $params = []): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/offers/offerwall_api/$adSlot";

        $queryParams = [
            'external_identifier' => $external_identifier,
            ...$params,
        ];

        $response = Http::timeout(5)->get($url, $queryParams);

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch offers');
        }

        return ServiceResponseDto::success()
            ->setMessage('Offers fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }

    public function getOffers(int $placementId, array $params = []): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/offers/get/{$placementId}";

        $queryParams = ['apiKey' => $this->apiKey, ...$params];

        $response = Http::timeout(5)->get($url, $queryParams);

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch offers');
        }

        return ServiceResponseDto::success()
            ->setMessage('Offers fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }

    public function getAccountDetails(): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/api2/account/details";

        $response = Http::timeout(5)->get($url, [
            'apiKey' => $this->apiKey,
        ]);

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch account details');
        }

        return ServiceResponseDto::success()
            ->setMessage('Account details fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }
}
