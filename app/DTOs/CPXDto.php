<?php

declare(strict_types=1);

namespace App\DTOs;

class CPXDto
{
    public function __construct(
        public ?string $extUserId,
        public ?string $ipUser
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            extUserId: $data['ext_user_id'] ?? null,
            ipUser: $data['ip_user'] ?? null
        );
    }
}
