<?php

declare(strict_types=1);

namespace App\DTOs\AyetStudio;

class AyetStudioWebhookDto
{
    public function __construct(
        public ?string $status,
        public ?string $transId,
        public ?string $userId,
        public ?string $amountUsd,
        public ?string $offerId,
        public ?string $hash,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            transId: $data['trans_id'] ?? null,
            userId: $data['user_id'] ?? null,
            amountUsd: $data['amount_usd'] ?? null,
            offerId: $data['offer_id'] ?? null,
            hash: $data['hash'] ?? null,
        );
    }
}
