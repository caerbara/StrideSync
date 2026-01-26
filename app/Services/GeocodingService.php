<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodingService
{
    public function reverseGeocodeCityState(float $lat, float $lng): array
    {
        $serpApiKey = env('SERPAPI_API_KEY');
        if ($serpApiKey) {
            $serp = $this->reverseGeocodeWithSerpApi($lat, $lng, $serpApiKey);
            if (!empty($serp['city']) || !empty($serp['state'])) {
                return $serp;
            }
        }

        $userAgent = env('NOMINATIM_USER_AGENT', 'StrideSync/1.0 (admin@stridesync.local)');
        $verify = filter_var(env('NOMINATIM_SSL_VERIFY', true), FILTER_VALIDATE_BOOLEAN);
        $response = Http::timeout(5)
            ->withOptions(['verify' => $verify])
            ->withHeaders(['User-Agent' => $userAgent])
            ->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $lat,
                'lon' => $lng,
                'zoom' => 10,
                'addressdetails' => 1,
            ]);

        if (!$response->ok()) {
            return ['city' => null, 'state' => null];
        }

        $payload = $response->json();
        if (!is_array($payload) || empty($payload['address'])) {
            return ['city' => null, 'state' => null];
        }

        $address = $payload['address'];
        $city = $address['city']
            ?? $address['town']
            ?? $address['village']
            ?? $address['municipality']
            ?? $address['county']
            ?? null;
        $state = $address['state'] ?? null;

        return [
            'city' => $city,
            'state' => $state,
        ];
    }

    private function reverseGeocodeWithSerpApi(float $lat, float $lng, string $apiKey): array
    {
        $engine = env('SERPAPI_ENGINE', 'google_maps');
        $zoom = env('SERPAPI_LL_ZOOM', '14z');
        $type = env('SERPAPI_TYPE', 'search');
        $query = env('SERPAPI_QUERY', '');

        $params = [
            'engine' => $engine,
            'api_key' => $apiKey,
            'll' => "@{$lat},{$lng},{$zoom}",
        ];

        if (is_string($type) && trim($type) !== '') {
            $params['type'] = $type;
        }

        if (is_string($query) && trim($query) !== '') {
            $params['q'] = trim($query);
        }

        $response = Http::timeout(8)->get('https://serpapi.com/search.json', $params);
        if (!$response->ok()) {
            return ['city' => null, 'state' => null];
        }

        $payload = $response->json();
        if (!is_array($payload)) {
            return ['city' => null, 'state' => null];
        }

        $address = null;
        if (isset($payload['place_results']['address'])) {
            $address = $payload['place_results']['address'];
        } elseif (isset($payload['place_results']['formatted_address'])) {
            $address = $payload['place_results']['formatted_address'];
        } elseif (isset($payload['local_results'][0]['address'])) {
            $address = $payload['local_results'][0]['address'];
        } elseif (isset($payload['knowledge_graph']['address'])) {
            $address = $payload['knowledge_graph']['address'];
        }

        if (is_string($address) && trim($address) !== '') {
            return $this->extractCityStateFromAddressString($address);
        }

        return ['city' => null, 'state' => null];
    }

    private function extractCityStateFromAddressString(string $address): array
    {
        $parts = array_map('trim', explode(',', $address));
        $parts = array_values(array_filter($parts, function ($part) {
            return $part !== '';
        }));

        $states = [
            'Johor',
            'Kedah',
            'Kelantan',
            'Melaka',
            'Melaka (Malacca)',
            'Negeri Sembilan',
            'Pahang',
            'Perak',
            'Perlis',
            'Pulau Pinang',
            'Pulau Pinang (Penang)',
            'Selangor',
            'Terengganu',
            'Sabah',
            'Sarawak',
            'Kuala Lumpur',
            'Putrajaya',
            'Labuan',
        ];

        $state = null;
        $stateIndex = null;
        foreach ($parts as $index => $part) {
            foreach ($states as $candidate) {
                if (stripos($part, $candidate) !== false) {
                    $state = $candidate;
                    $stateIndex = $index;
                    break 2;
                }
            }
        }

        $city = null;
        if ($stateIndex !== null && $stateIndex > 0) {
            $city = $parts[$stateIndex - 1] ?? null;
        } elseif (!empty($parts)) {
            $city = $parts[0];
        }

        if (is_string($city)) {
            $city = preg_replace('/^\\d+\\s*/', '', $city);
            $city = trim((string) $city);
        }

        return [
            'city' => $city !== '' ? $city : null,
            'state' => $state,
        ];
    }
}
