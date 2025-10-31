<?php

namespace TenderService\Services;

class JsonLoaderService
{
    public function loadFromUrl(string $url): ?array
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'TenderService/1.0'
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);

        $json = file_get_contents($url, false, $context);
        
        if ($json === false) {
            throw new \Exception("Failed to load JSON from URL: {$url}");
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON decode error: " . json_last_error_msg());
        }

        return $data;
    }
}
