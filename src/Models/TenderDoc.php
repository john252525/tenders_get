<?php

namespace TenderService\Models;

use DateTime;
use JsonSerializable;

class TenderDoc implements JsonSerializable
{
    public ?int $id = null;
    public int $tender_id;
    public string $doc_type;
    public ?DateTime $published_at;

    public static function fromArray(array $data): self
    {
        $doc = new self();
        $doc->tender_id = (int)$data['tender_id'];
        $doc->doc_type = $data['doc_type'];
        $doc->published_at = Tender::parseDateTime($data['published_at'] ?? null);
        
        return $doc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'tender_id' => $this->tender_id,
            'doc_type' => $this->doc_type,
            'published_at' => $this->published_at?->format('c')
        ];
    }
}
