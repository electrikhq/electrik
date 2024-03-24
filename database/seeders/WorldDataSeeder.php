<?php

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class WorldDataSeeder extends Seeder
{
    public function run()
    {
        $path = database_path('json/world.json');
        $data = json_decode(file_get_contents($path), true);
        
        foreach ($data as $countryData) {
            $country = Country::create([
                'name' => $countryData['name'],
                'iso3' => $countryData['iso3'],
                'iso2' => $countryData['iso2'],
                // Add other country fields
            ]);

            foreach ($countryData['states'] as $stateData) {
                $state = $country->states()->create([
                    'name' => $stateData['name'],
                    // Add other state fields
                ]);

                foreach ($stateData['cities'] as $cityData) {
                    $state->cities()->create([
                        'name' => $cityData['name'],
                        // Add other city fields
                    ]);
                }
            }
        }
    }
}
