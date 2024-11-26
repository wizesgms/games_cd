<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserPlayer;

use Illuminate\Support\Str;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $player = new UserPlayer();
        $player->ext_player = "32rrcdssr";
        $player->playerid = Str::random(6);
        $player->password = Str::random(6);
        $player->agent_code = "sistem";
        $player->balance = 150000;
        $player->status = 1;
        $player->save();
    }
}
