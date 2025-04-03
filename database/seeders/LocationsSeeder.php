<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ['name' => 'Abu Dhabi', 'latitude' => '24.453884', 'longitude' => '54.377344', 'gau_code' => 'AUH'],
            ['name' => 'Dubai', 'latitude' => '25.276987', 'longitude' => '55.296249', 'gau_code' => 'DXB'],
            ['name' => 'Sharjah', 'latitude' => '25.346255', 'longitude' => '55.420932', 'gau_code' => 'SHJ'],
            ['name' => 'Ajman', 'latitude' => '25.399514', 'longitude' => '55.479659', 'gau_code' => 'AJM'],
            ['name' => 'Ras Al Khaimah', 'latitude' => '25.789533', 'longitude' => '55.943207', 'gau_code' => 'RAK'],
            ['name' => 'Fujairah', 'latitude' => '25.128809', 'longitude' => '56.326485', 'gau_code' => 'FUJ'],
            ['name' => 'Umm Al Quwain', 'latitude' => '25.564735', 'longitude' => '55.555174', 'gau_code' => 'UAQ'],
            ['name' => 'Al Ain', 'latitude' => '24.2075', 'longitude' => '55.744721', 'gau_code' => 'ALN'],
        ];

        DB::table('locations')->insert($locations);
    }
}
