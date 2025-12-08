<?php

declare(strict_types=1);

namespace App\DTOs\AyetStudio;

class AyetStudioDto
{
    public function __construct(
        public ?string $adSlot,
        public ?string $externalIdentifier,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            adSlot: $data['ad_slot'] ?? null,
            externalIdentifier: $data['external_identifier'] ?? null,
        );
    }
}
