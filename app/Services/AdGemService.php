<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\AdGem\AdGemWebhookDto;
use App\DTOs\AdGem\GenerateOfferLinkDto;
use App\DTOs\ResponseDto\ServiceResponseDto;
use Illuminate\Support\Facades\Http;

class AdGemService
{
    protected ?string $apiKey;

    protected ?string $baseUrl;

    protected ?string $secret;

    public function __construct()
    {
        $this->apiKey = config('services.adgem.api_key');
        $this->secret = config('services.adgem.secret');
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

    public function generateOfferLink(GenerateOfferLinkDto $dto, array $extraParams = []): string
    {
        $params = [
            'user_id' => $dto->userId,
            'offer_id' => $dto->offerId,
            ...$extraParams,
        ];

        $payload = implode('|', [$params['user_id'], $params['offer_id']]);
        $params['hash'] = hash_hmac('sha256', $payload, $this->secret);

        return 'https://api.adgem.com/offers?'.http_build_query($params);
    }

    public function onOfferComplete(AdGemWebhookDto $dto): ServiceResponseDto
    {
        $payload = implode('|', [$dto->userId, $dto->transactionId, $dto->state ?? '']);
        $calculatedHash = hash_hmac('sha256', $payload, $this->secret);

        if (! hash_equals($calculatedHash, $dto->hash)) {
            logger()->warning('AdGem webhook hash verification failed', $dto->toArray());

            return ServiceResponseDto::error('Invalid hash', 400);
        }

        return ServiceResponseDto::success()
            ->setMessage('ok')
            ->setStatus(200);
    }
}
