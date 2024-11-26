<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('provider_lists')->insert([
            [
                'ProviderName' => 'Asia Gaming',
                'ProviderCode' => 1001,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Big Gaming',
                'ProviderCode' => 1004,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Pragmatic Play',
                'ProviderCode' => 1006,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'PG Soft',
                'ProviderCode' => 1007,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Game Play Interactive',
                'ProviderCode' => 1008,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'CQ9',
                'ProviderCode' => 1009,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Joker',
                'ProviderCode' => 1013,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Dragon Soft',
                'ProviderCode' => 1014,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Yggdrasil',
                'ProviderCode' => 1027,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'SV388 Cock Fighting',
                'ProviderCode' => 1033,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Spade Gaming',
                'ProviderCode' => 1034,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Vivo Gaming',
                'ProviderCode' => 1035,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'UG Sport',
                'ProviderCode' => 1036,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Habanero',
                'ProviderCode' => 1041,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'IBC',
                'ProviderCode' => 1046,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Evoplay',
                'ProviderCode' => 1049,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Skywind',
                'ProviderCode' => 1077,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'CMD368',
                'ProviderCode' => 1078,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'BTI',
                'ProviderCode' => 1081,
                'ProviderId' => $this->uid()
            ],[
                'ProviderName' => 'Advant Play',
                'ProviderCode' => 1084,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Jili',
                'ProviderCode' => 1091,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'N2Live',
                'ProviderCode' => 1096,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'SSports',
                'ProviderCode' => 1104,
                'ProviderId' => $this->uid()
            ],

            [
                'ProviderName' => 'Netent',
                'ProviderCode' => 1109,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Red Tiger',
                'ProviderCode' => 1110,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'No Limit City',
                'ProviderCode' => 1146,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'Live22SM',
                'ProviderCode' => 1150,
                'ProviderId' => $this->uid()
            ],
            [
                'ProviderName' => 'FASTSPIN',
                'ProviderCode' => 1151,
                'ProviderId' => $this->uid()
            ]
        ]);
    }

    public function uid($length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
