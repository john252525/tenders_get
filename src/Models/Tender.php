<?php

namespace TenderService\Models;

use DateTime;
use JsonSerializable;

class Tender implements JsonSerializable
{
    public ?int $id = null;
    public string $purchase_number;
    public ?string $object_info;
    public ?float $max_price;
    public ?string $currency_code;
    public ?DateTime $published_at;
    public ?DateTime $collecting_finished_at;
    public ?string $purchase_type;
    public ?int $region;
    public ?int $stage;
    public ?array $customers;
    public ?array $ikzs;
    public ?DateTime $created_at;

    public array $docs = [];

    public static function fromArray(array $data): self
    {
        $tender = new self();
        $tender->purchase_number = $data['purchase_number'];
        $tender->object_info = $data['object_info'] ?? null;
        $tender->max_price = isset($data['max_price']) ? (float)$data['max_price'] : null;
        $tender->currency_code = $data['currency_code'] ?? null;
        $tender->published_at = self::parseDateTime($data['published_at'] ?? null);
        $tender->collecting_finished_at = self::parseDateTime($data['collecting_finished_at'] ?? null);
        $tender->purchase_type = $data['purchase_type'] ?? null;
        $tender->region = isset($data['region']) ? (int)$data['region'] : null;
        $tender->stage = isset($data['stage']) ? (int)$data['stage'] : null;
        $tender->customers = $data['customers'] ?? null;
        $tender->ikzs = $data['ikzs'] ?? null;

        return $tender;
    }

    private static function parseDateTime(?string $dateString): ?DateTime
    {
        if (!$dateString) {
            return null;
        }

        try {
            return new DateTime($dateString);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'purchase_number' => $this->purchase_number,
            'object_info' => $this->object_info,
            'max_price' => $this->max_price,
            'currency_code' => $this->currency_code,
            'published_at' => $this->published_at?->format('c'),
            'collecting_finished_at' => $this->collecting_finished_at?->format('c'),
            'purchase_type' => $this->purchase_type,
            'region' => $this->region,
            'stage' => $this->stage,
            'customers' => $this->customers,
            'ikzs' => $this->ikzs,
            'docs' => $this->docs
        ];
    }
}
