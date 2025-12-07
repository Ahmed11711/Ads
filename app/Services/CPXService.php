<?php

namespace App\Services;

use App\DTOs\CPXDto;
use App\DTOs\ResponseDto\ServiceResponseDto;
use Illuminate\Support\Facades\Http;

class CPXService
{
    protected ?string $appId;

    protected ?string $baseUrl;

    protected ?string $hash;

    public function __construct()
    {
        $this->appId = config('services.cpx.app_id');
        $this->baseUrl = config('services.cpx.url', 'https://live-api.cpx-research.com/api');
        $this->hash = config('services.cpx.hash');
    }

    public function getServeys(CPXDto $dto, array $params = []): ServiceResponseDto
    {
        $url = "{$this->baseUrl}/get-surveys.php";

        $queryParam = [
            'app_id' => $this->appId,
            'ext_user_id' => $dto->extUserId,
            'ip_user' => $dto->ipUser,
            'secure_hash' => $this->hash,
            'output_method' => 'api',
            ...$params,
        ];

        $response = Http::timeout(5)->get($url, $queryParam);

        if (! $response->successful()) {
            return ServiceResponseDto::error('Failed to fetch surveys');
        }

        return ServiceResponseDto::success()
            ->setMessage('Surveys fetched successfully')
            ->setData($response->json())
            ->setStatus(200);
    }
}
