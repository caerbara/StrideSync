<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DistanceController extends Controller
{
    public function routeDistance(Request $request, GeocodingService $geocoding): \Illuminate\Http\JsonResponse
    {
        $originLat = $request->query('origin_lat');
        $originLng = $request->query('origin_lng');
        $destination = trim((string) $request->query('destination', ''));
        $destLat = $request->query('destination_lat');
        $destLng = $request->query('destination_lng');

        if (!is_numeric($originLat) || !is_numeric($originLng)) {
            return response()->json(['error' => 'Invalid origin coordinates'], 422);
        }

        $originLat = (float) $originLat;
        $originLng = (float) $originLng;
        $origin = $originLat . ',' . $originLng;

        $destinationCoords = null;
        if ($destination === '' && is_numeric($destLat) && is_numeric($destLng)) {
            $destinationCoords = [(float) $destLat, (float) $destLng];
            $destination = $destinationCoords[0] . ',' . $destinationCoords[1];
        }

        if ($destination === '') {
            return response()->json(['error' => 'Invalid destination'], 422);
        }

        $cacheKey = 'route_distance:' . md5(mb_strtolower($origin . '|' . $destination));
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && isset($cached['distance_km'])) {
            return response()->json($cached);
        }

        $result = [
            'distance_km' => null,
            'duration_text' => null,
            'source' => 'route_unavailable',
        ];

        $serpApiKey = env('SERPAPI_API_KEY');
        if ($serpApiKey) {
            $serpResponse = Http::timeout(8)->get('https://serpapi.com/search.json', [
                'engine' => 'google_maps_directions',
                'api_key' => $serpApiKey,
                'origin' => $origin,
                'destination' => $destination,
                'travel_mode' => 'driving',
            ]);

            if ($serpResponse->ok()) {
                $payload = $serpResponse->json();
                $route = is_array($payload['routes'] ?? null) ? ($payload['routes'][0] ?? null) : null;
                $distanceKm = $this->extractDistanceKm($route);
                if (is_numeric($distanceKm)) {
                    $result['distance_km'] = (float) $distanceKm;
                    $result['duration_text'] = $this->extractDurationText($route);
                    $result['source'] = 'serpapi';
                }
            }
        }

        if (!is_numeric($result['distance_km'])) {
            if ($destinationCoords === null) {
                $geo = $geocoding->geocodeLocationName($destination);
                if (is_array($geo) && isset($geo['lat'], $geo['lng'])) {
                    $destinationCoords = [(float) $geo['lat'], (float) $geo['lng']];
                }
            }

            if ($destinationCoords !== null) {
                $osrmResponse = Http::timeout(8)->get(sprintf(
                    'https://router.project-osrm.org/route/v1/driving/%s,%s;%s,%s',
                    $originLng,
                    $originLat,
                    $destinationCoords[1],
                    $destinationCoords[0]
                ), [
                    'overview' => 'false',
                ]);

                if ($osrmResponse->ok()) {
                    $payload = $osrmResponse->json();
                    $route = is_array($payload['routes'] ?? null) ? ($payload['routes'][0] ?? null) : null;
                    if (is_array($route)) {
                        $meters = $route['distance'] ?? null;
                        if (is_numeric($meters)) {
                            $result['distance_km'] = ((float) $meters) / 1000;
                            $result['source'] = 'osrm';
                        }

                        $durationSeconds = $route['duration'] ?? null;
                        if (is_numeric($durationSeconds)) {
                            $minutes = (int) round(((float) $durationSeconds) / 60);
                            $hours = (int) floor($minutes / 60);
                            $mins = $minutes % 60;
                            $result['duration_text'] = $hours > 0
                                ? sprintf('%dh %dm', $hours, $mins)
                                : sprintf('%dm', $mins);
                        }
                    }
                }
            }
        }

        if (!is_numeric($result['distance_km'])) {
            return response()->json(['error' => 'Distance unavailable'], 404);
        }

        $result['distance_km'] = round((float) $result['distance_km'], 1);
        Cache::put($cacheKey, $result, now()->addMinutes(30));

        return response()->json($result);
    }

    private function extractDistanceKm(?array $route): ?float
    {
        if (!is_array($route)) {
            return null;
        }

        $meters = $route['distance_in_meters'] ?? $route['distance_meters'] ?? null;
        if (is_numeric($meters)) {
            return ((float) $meters) / 1000;
        }

        $distanceText = $route['distance'] ?? null;
        if (!is_string($distanceText) || trim($distanceText) === '') {
            return null;
        }

        $distanceText = trim($distanceText);
        if (preg_match('/([0-9.,]+)\s*(km|kilometer|kilometers|m|meter|meters|mi|mile|miles)/i', $distanceText, $matches)) {
            $value = (float) str_replace(',', '', $matches[1]);
            $unit = strtolower($matches[2]);
            if (in_array($unit, ['m', 'meter', 'meters'], true)) {
                return $value / 1000;
            }
            if (in_array($unit, ['mi', 'mile', 'miles'], true)) {
                return $value * 1.60934;
            }
            return $value;
        }

        return null;
    }

    private function extractDurationText(?array $route): ?string
    {
        if (!is_array($route)) {
            return null;
        }

        $duration = $route['duration'] ?? null;
        if (is_string($duration) && trim($duration) !== '') {
            return trim($duration);
        }

        $time = $route['travel_time'] ?? null;
        if (is_string($time) && trim($time) !== '') {
            return trim($time);
        }

        return null;
    }
}
