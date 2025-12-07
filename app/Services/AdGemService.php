<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ResponseDto\ServiceResponseDto;
use Illuminate\Support\Facades\Http;

class AdGemService
{
    protected ?string $apiKey;

    protected ?string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.adgem.api_key');
        $this->baseUrl = config('services.adgem.url', 'https://offer-api.adgem.com');
    }

    public function getOffers(array $params = []): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/v1/offers";

        $queryParam = [
            ...$params,
        ];

        $response = Http::timeout(5)
            ->withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
            ])
            ->get($url, $queryParam);

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch offers');
        }

        return ServiceResponseDto::success()
            ->setMessage('Offers fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }
}
