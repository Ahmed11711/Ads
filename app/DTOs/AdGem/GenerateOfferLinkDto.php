<?php

declare(strict_types=1);

namespace App\DTOs\AdGem;

class GenerateOfferLinkDto
{
    public function __construct(
        public ?string $userId,
        public ?string $offerId
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            offerId: $data['offer_id'] ?? null
        );
    }
}
