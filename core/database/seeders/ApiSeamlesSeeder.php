<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiSeamles;

class ApiSeamlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apis = new ApiSeamles();
        $apis->agentcode = "E804";
        $apis->secretkey = "H5bnr4";
        $apis->url = "https://swmd.6633663.com ";
        $apis->save();
    }
}
