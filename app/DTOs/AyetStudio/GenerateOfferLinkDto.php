<?php

declare(strict_types=1);

namespace App\DTOs\AyetStudio;

class GenerateOfferLinkDto
{
    public function __construct(
        public ?string $userId,
        public ?string $offerId,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            offerId: $data['offer_id'] ?? null,
        );
    }
}
