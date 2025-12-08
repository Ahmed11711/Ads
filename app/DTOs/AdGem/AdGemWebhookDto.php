<?php

declare(strict_types=1);

namespace App\DTOs\AdGem;

class AdGemWebhookDto
{
    public function __construct(
        public ?string $appId,
        public ?string $userId,
        public ?string $transactionId,
        public ?string $goalName,
        public ?string $offerId,
        public ?string $hash = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            appId: $data['app_id'] ?? null,
            userId: $data['user_id'] ?? null,
            transactionId: $data['transaction_id'] ?? null,
            goalName: $data['goal_name'] ?? null,
            offerId: $data['offer_id'] ?? null,
            hash: $data['hash'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'app_id' => $this->appId,
            'user_id' => $this->userId,
            'transaction_id' => $this->transactionId,
            'goal_name' => $this->goalName,
            'offer_id' => $this->offerId,
            'hash' => $this->hash,
        ];
    }
}
