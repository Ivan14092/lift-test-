<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoLocationService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    public function getCountryByIp(string $ip): string
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                "https://www.iplocate.io/api/lookup/{$ip}"
            );

            $data = $response->toArray();

            return $data['country'] ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}