<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;

class GeocodeController extends Controller
{
    public function locationSearch(Request $request, GeocodingService $geocodingService)
    {
        $query = trim((string) $request->query('query', ''));
        if ($query === '') {
            return response()->json(['error' => 'Missing query'], 422);
        }

        $coords = $geocodingService->geocodeLocationName($query);
        if (!$coords) {
            return response()->json(['error' => 'Unable to geocode'], 404);
        }

        return response()->json([
            'lat' => $coords['lat'],
            'lng' => $coords['lng'],
        ]);
    }
}
