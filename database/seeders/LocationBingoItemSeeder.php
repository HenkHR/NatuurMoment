<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationBingoItem;
use Illuminate\Database\Seeder;

class LocationBingoItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $location = Location::firstOrCreate(
            ['name' => 'Veluwezoom'],
            [
                'description' => 'Een prachtig natuurgebied op de Veluwe met bossen, heide en wilde dieren.',
                'province' => 'Gelderland',
                'duration' => 90,
            ]
        );

        $bingoItems = [
            [
                'label' => 'Paddenstoel',
                'points' => 10,
                'fact' => 'Paddenstoelen zijn geen planten maar schimmels. Ze helpen bomen om voedingsstoffen uit de grond te halen.',
            ],
            [
                'label' => 'Eikenboom',
                'points' => 10,
                'fact' => 'Een eikenboom kan wel 800 jaar oud worden en biedt een thuis aan meer dan 500 soorten insecten.',
            ],
            [
                'label' => 'Vogelnest',
                'points' => 20,
                'fact' => 'Vogels bouwen hun nest van takjes, mos en zelfs spinnenweb. Sommige nesten wegen wel 2 kilo!',
            ],
            [
                'label' => 'Specht',
                'points' => 30,
                'fact' => 'Een specht kan wel 20 keer per seconde met zijn snavel op een boom tikken zonder hoofdpijn te krijgen.',
            ],
            [
                'label' => 'Eekhoorn',
                'points' => 25,
                'fact' => 'Eekhoorns verstoppen duizenden nootjes per jaar, maar vergeten de helft. Zo planten ze onbewust nieuwe bomen!',
            ],
            [
                'label' => 'Dennenappel',
                'points' => 10,
                'fact' => 'Dennenappels gaan dicht als het regent en open als het droog is. Zo beschermen ze hun zaden.',
            ],
            [
                'label' => 'Varen',
                'points' => 15,
                'fact' => 'Varens bestaan al 360 miljoen jaar - ze waren er al voordat de dinosaurussen leefden!',
            ],
            [
                'label' => 'Konijn',
                'points' => 20,
                'fact' => 'Konijnen kunnen hun oren onafhankelijk van elkaar draaien om geluiden uit alle richtingen te horen.',
            ],
            [
                'label' => 'Vlinder',
                'points' => 25,
                'fact' => 'Vlinders proeven met hun pootjes! Ze hebben smaakzintuigen aan hun voeten om bloemen te herkennen.',
            ],
        ];

        foreach ($bingoItems as $item) {
            LocationBingoItem::firstOrCreate(
                [
                    'location_id' => $location->id,
                    'label' => $item['label'],
                ],
                [
                    'points' => $item['points'],
                    'fact' => $item['fact'],
                ]
            );
        }
    }
}
