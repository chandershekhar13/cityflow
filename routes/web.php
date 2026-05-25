<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Models\Zone;

/*
|--------------------------------------------------------------------------
| Welcome Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('welcome');

});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    // Weather API

    $weatherResponse = Http::get(
        'https://api.openweathermap.org/data/2.5/weather',
        [
            'lat' => 30.7333,
            'lon' => 76.7794,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]
    );

    $weather = $weatherResponse->json();

    // Zones

    $zones = Zone::all();

    // Analytics

    $totalCitizens =
        Zone::sum('population');

    $activeZones =
        Zone::count();

    $averageTraffic =
        Zone::avg('traffic_level');

    return view('dashboard', [

        'weather' => $weather,

        'zones' => $zones,

        'totalCitizens' => $totalCitizens,

        'activeZones' => $activeZones,

        'averageTraffic' => round(
            $averageTraffic
        ),

    ]);

})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Map Page
|--------------------------------------------------------------------------
*/

Route::get('/map', function () {

    $zones = Zone::all();

    $weatherResponse = Http::get(
        'https://api.openweathermap.org/data/2.5/weather',
        [
            'lat' => 30.7333,
            'lon' => 76.7794,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]
    );

    $weather = $weatherResponse->json();

    return view('map', [

        'zones' => $zones,

        'weather' => $weather

    ]);

})->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| Weather Data API
|--------------------------------------------------------------------------
*/

Route::get('/weather-data', function () {

    $response = Http::get(
        'https://api.openweathermap.org/data/2.5/weather',
        [
            'lat' => 30.7333,
            'lon' => 76.7794,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]
    );

    return response()->json(
        $response->json()
    );

});

/*
|--------------------------------------------------------------------------
| Movement Simulation API
|--------------------------------------------------------------------------
*/

Route::get('/movement-data', function () {

    // Weather

    $weatherResponse = Http::get(
        'https://api.openweathermap.org/data/2.5/weather',
        [
            'lat' => 30.7333,
            'lon' => 76.7794,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]
    );

    $weather = $weatherResponse->json();

    $condition =
        $weather['weather'][0]['main'];

    // Zones

    $zones = Zone::all();

    $movements = [];

    foreach ($zones as $from) {

        $to = $zones->random();

        if ($from->id !== $to->id) {

            $basePeople =
                rand(50, 500);

            // Weather Effect

            if ($condition === 'Rain') {
                $basePeople *= 0.5;
            }

            if ($condition === 'Clear') {
                $basePeople *= 1.3;
            }

            if ($condition === 'Clouds') {
                $basePeople *= 1.1;
            }

            $movements[] = [

                'from' => [

                    'name' =>
                        $from->name,

                    'lat' =>
                        $from->latitude,

                    'lng' =>
                        $from->longitude,
                ],

                'to' => [

                    'name' =>
                        $to->name,

                    'lat' =>
                        $to->latitude,

                    'lng' =>
                        $to->longitude,
                ],

                'people' =>
                    round($basePeople),

                'transport' =>
                    collect([
                        'Bus',
                        'Car',
                        'Metro',
                        'Bike'
                    ])->random(),

                'weather' =>
                    $condition,
            ];
        }
    }

    return response()->json(
        $movements
    );

});

/*
|--------------------------------------------------------------------------
| Live Dashboard API
|--------------------------------------------------------------------------
*/


Route::get('/dashboard-data', function () {

    // Weather API

    $weatherResponse = Http::get(
        'https://api.openweathermap.org/data/2.5/weather',
        [
            'lat' => 30.7333,
            'lon' => 76.7794,
            'appid' => env('OPENWEATHER_API_KEY'),
            'units' => 'metric'
        ]
    );

    $weather = $weatherResponse->json();

    $condition =
        $weather['weather'][0]['main'];

    // Base Traffic

    $baseTraffic =
        rand(50, 95);

    // Weather Effect

    if ($condition === 'Rain') {
        $baseTraffic -= 20;
    }

    if ($condition === 'Clear') {
        $baseTraffic += 10;
    }

    if ($condition === 'Clouds') {
        $baseTraffic += 5;
    }

    // Zones

    $zones = Zone::all();

    $liveZones = [];

    $totalLivePopulation = 0;

    foreach ($zones as $zone) {

        /*
        |--------------------------------------------------------------------------
        | Simulated Incoming / Outgoing Movement
        |--------------------------------------------------------------------------
        */

        $incoming =
            rand(100, 1000);

        $outgoing =
            rand(100, 1000);

        /*
        |--------------------------------------------------------------------------
        | Weather Impact
        |--------------------------------------------------------------------------
        */

        if ($condition === 'Rain') {

            $incoming *= 0.7;

            $outgoing *= 0.7;
        }

        if ($condition === 'Clear') {

            $incoming *= 1.2;

            $outgoing *= 1.2;
        }

        /*
        |--------------------------------------------------------------------------
        | Dynamic Population
        |--------------------------------------------------------------------------
        */

        $livePopulation =

            $zone->population

            + $incoming

            - $outgoing;

        // Prevent Unrealistic Negative Values

        $livePopulation =
            max(1000, round($livePopulation));

        /*
        |--------------------------------------------------------------------------
        | Dynamic Traffic
        |--------------------------------------------------------------------------
        */

        $traffic =
            rand(40, 95);

        // Higher Population = Higher Traffic

        if ($livePopulation > 16000) {

            $traffic += 15;
        }

        // Weather Influence

        if ($condition === 'Rain') {

            $traffic -= 20;
        }

        if ($condition === 'Clear') {

            $traffic += 10;
        }

        $traffic = max(
            10,
            min(100, $traffic)
        );

        /*
        |--------------------------------------------------------------------------
        | Save Zone Data
        |--------------------------------------------------------------------------
        */

        $liveZones[] = [

            'name' =>
                $zone->name,

            'population' =>
                $livePopulation,

            'traffic_level' =>
                $traffic,

            'incoming' =>
                round($incoming),

            'outgoing' =>
                round($outgoing),
        ];

        // Running Total Population

        $totalLivePopulation +=
            $livePopulation;
    }

    /*
    |--------------------------------------------------------------------------
    | Live Activity Feed
    |--------------------------------------------------------------------------
    */

    $activities = [];

    foreach (range(1, 5) as $i) {

        $from =
            $zones->random();

        $to =
            $zones->random();

        $movementPeople =
            rand(50, 500);

        $activities[] = [

            'message' =>

                $movementPeople

                . ' people moved from '

                . $from->name

                . ' to '

                . $to->name,

            'time' =>
                now()->format('H:i:s')
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Return JSON Response
    |--------------------------------------------------------------------------
    */

    return response()->json([

        'weather' => [

            'temp' =>
                round(
                    $weather['main']['temp']
                ),

            'condition' =>
                $condition
        ],

        'traffic' => max(
            10,
            min(100, $baseTraffic)
        ),

        // Dynamic Total Population

        'citizens' =>
            round($totalLivePopulation),

        'zones' =>
            Zone::count(),

        // Dynamic Zones

        'zonesData' =>
            $liveZones,

        // Activity Feed

        'activities' =>
            $activities

    ]);

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
