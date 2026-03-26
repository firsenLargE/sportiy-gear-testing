<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;
use App\Models\Province;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            'Koshi' => [
                'Bhojpur',
                'Dhankuta',
                'Ilam',
                'Jhapa',
                'Khotang',
                'Morang',
                'Okhaldhunga',
                'Panchthar',
                'Sankhuwasabha',
                'Sunsari',
                'Solukhumbu',
                'Saptari',
                'Siraha',
                'Taplejung',
                'Terhathum',
                'Udayapur'
            ],
            'Madhesh' => [
                'Bara',
                'Dhanusha',
                'Mahottari',
                'Parsa',
                'Rautahat',
                'Saptari',
                'Sarlahi',
                'Siraha'
            ],
            'Bagmati' => [
                'Bhaktapur',
                'Chitwan',
                'Dhading',
                'Dolakha',
                'Kathmandu',
                'Kavrepalanchok',
                'Lalitpur',
                'Makwanpur',
                'Nuwakot',
                'Ramechhap',
                'Rasuwa',
                'Sindhuli',
                'Sindhupalchok'
            ],
            'Gandaki' => [
                'Baglung',
                'Gorkha',
                'Kaski',
                'Lamjung',
                'Manang',
                'Mustang',
                'Myagdi',
                'Nawalpur',
                'Parbat',
                'Syangja',
                'Tanahun'
            ],
            'Lumbini' => [
                'Arghakhanchi',
                'Banke',
                'Bardiya',
                'Dang',
                'Gulmi',
                'Kapilvastu',
                'Nawalparasi',
                'Palpa',
                'Pyuthan',
                'Rolpa',
                'Rukum East',
                'Rupandehi'
            ],
            'Karnali' => [
                'Dailekh',
                'Dolpa',
                'Humla',
                'Jajarkot',
                'Jumla',
                'Kalikot',
                'Mugu',
                'Salyan',
                'Surkhet',
                'Rukum West'
            ],
            'Sudurpashchim' => [
                'Achham',
                'Baitadi',
                'Bajhang',
                'Bajura',
                'Dadeldhura',
                'Darchula',
                'Doti',
                'Kailali',
                'Kanchanpur'
            ]
        ];

        foreach ($districts as $provinceName => $districtList) {
            $province = Province::where('name', $provinceName)->first();
            foreach ($districtList as $districtName) {
                District::create([
                    'province_id' => $province->id,
                    'name' => $districtName
                ]);
            }
        }
    }
}
