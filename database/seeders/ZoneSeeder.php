<?php

namespace Database\Seeders;
use App\Models\Zone;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
   

public function run(): void
{

$zones = [

    [
        'name' => 'Junction 27',
        'population' => 12000,
        'traffic_level' => 85,
        'latitude' => 30.7333,
        'longitude' => 76.7794,
    ],

    [
        'name' => 'City Bird Sanctuary',
        'population' => 8000,
        'traffic_level' => 70,
        'latitude' => 30.7290,
        'longitude' => 76.7800,
    ],

    [
        'name' => 'Sector 22',
        'population' => 15000,
        'traffic_level' => 90,
        'latitude' => 30.7350,
        'longitude' => 76.7750,
    ],

    [
        'name' => 'Udyog Path',
        'population' => 10000,
        'traffic_level' => 95,
        'latitude' => 30.7310,
        'longitude' => 76.7820,
    ],

];



    foreach ($zones as $zone) {
        Zone::create($zone);
    }
}
}
