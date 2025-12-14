<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationRouteStop;
use Illuminate\Database\Seeder;

class LocationRouteStopSeeder extends Seeder
{
    /**
     * Seed multiple choice questions (route stops) for locations.
     */
    public function run(): void
    {
        $this->seedVeluwezoomRouteStops();
        $this->seedTestLocatieRouteStops();
    }

    private function seedVeluwezoomRouteStops(): void
    {
        $location = Location::where('name', 'Veluwezoom')->first();

        if (!$location) {
            return;
        }

        $routeStops = [
            [
                'name' => 'De Eikenboom',
                'question_text' => 'Hoe oud kan een eikenboom maximaal worden?',
                'option_a' => 'Ongeveer 100 jaar',
                'option_b' => 'Ongeveer 300 jaar',
                'option_c' => 'Ongeveer 800 jaar',
                'option_d' => 'Ongeveer 50 jaar',
                'correct_option' => 'C',
                'points' => 10,
                'sequence' => 1,
            ],
            [
                'name' => 'Paddenstoelen Paradijs',
                'question_text' => 'Wat is een paddenstoel eigenlijk?',
                'option_a' => 'Een plant',
                'option_b' => 'Een schimmel',
                'option_c' => 'Een dier',
                'option_d' => 'Een bacterie',
                'correct_option' => 'B',
                'points' => 10,
                'sequence' => 2,
            ],
            [
                'name' => 'De Specht',
                'question_text' => 'Hoe vaak per seconde kan een specht met zijn snavel op een boom tikken?',
                'option_a' => '5 keer',
                'option_b' => '10 keer',
                'option_c' => '20 keer',
                'option_d' => '2 keer',
                'correct_option' => 'C',
                'points' => 15,
                'sequence' => 3,
            ],
            [
                'name' => 'Eekhoorns en Noten',
                'question_text' => 'Waarom planten eekhoorns onbewust nieuwe bomen?',
                'option_a' => 'Ze eten alleen rotte noten',
                'option_b' => 'Ze vergeten waar ze noten hebben verstopt',
                'option_c' => 'Ze graven gaten voor andere dieren',
                'option_d' => 'Ze laten noten uit bomen vallen',
                'correct_option' => 'B',
                'points' => 15,
                'sequence' => 4,
            ],
            [
                'name' => 'Oude Varens',
                'question_text' => 'Hoe lang bestaan varens al op aarde?',
                'option_a' => '1 miljoen jaar',
                'option_b' => '50 miljoen jaar',
                'option_c' => '360 miljoen jaar',
                'option_d' => '10.000 jaar',
                'correct_option' => 'C',
                'points' => 20,
                'sequence' => 5,
            ],
            [
                'name' => 'Vlinder Zintuigen',
                'question_text' => 'Waarmee proeven vlinders hun voedsel?',
                'option_a' => 'Met hun tong',
                'option_b' => 'Met hun vleugels',
                'option_c' => 'Met hun pootjes',
                'option_d' => 'Met hun antennes',
                'correct_option' => 'C',
                'points' => 20,
                'sequence' => 6,
            ],
            [
                'name' => 'Konijnenoren',
                'question_text' => 'Wat is bijzonder aan de oren van een konijn?',
                'option_a' => 'Ze kunnen geluiden uit de toekomst horen',
                'option_b' => 'Ze kunnen onafhankelijk van elkaar draaien',
                'option_c' => 'Ze kunnen kleuren zien',
                'option_d' => 'Ze kunnen onder water horen',
                'correct_option' => 'B',
                'points' => 15,
                'sequence' => 7,
            ],
            [
                'name' => 'Dennenappel Weer',
                'question_text' => 'Wat doen dennenappels als het regent?',
                'option_a' => 'Ze worden groter',
                'option_b' => 'Ze vallen van de boom',
                'option_c' => 'Ze gaan dicht',
                'option_d' => 'Ze veranderen van kleur',
                'correct_option' => 'C',
                'points' => 10,
                'sequence' => 8,
            ],
        ];

        foreach ($routeStops as $stop) {
            LocationRouteStop::firstOrCreate(
                [
                    'location_id' => $location->id,
                    'sequence' => $stop['sequence'],
                ],
                $stop
            );
        }
    }

    private function seedTestLocatieRouteStops(): void
    {
        $location = Location::where('name', 'TestLocatie')->first();

        if (!$location) {
            return;
        }

        $routeStops = [
            [
                'name' => 'Bomen Herkennen',
                'question_text' => 'Welke boom verliest zijn bladeren NIET in de winter?',
                'option_a' => 'Eikenboom',
                'option_b' => 'Beukenboom',
                'option_c' => 'Dennenboom',
                'option_d' => 'Berkenboom',
                'correct_option' => 'C',
                'points' => 10,
                'sequence' => 1,
            ],
            [
                'name' => 'Vogels Spotten',
                'question_text' => 'Welke vogel staat bekend om zijn rode borstje?',
                'option_a' => 'Merel',
                'option_b' => 'Roodborstje',
                'option_c' => 'Mus',
                'option_d' => 'Kraai',
                'correct_option' => 'B',
                'points' => 10,
                'sequence' => 2,
            ],
            [
                'name' => 'Insecten Wereld',
                'question_text' => 'Hoeveel poten heeft een insect?',
                'option_a' => '4 poten',
                'option_b' => '6 poten',
                'option_c' => '8 poten',
                'option_d' => '10 poten',
                'correct_option' => 'B',
                'points' => 10,
                'sequence' => 3,
            ],
            [
                'name' => 'Water en Natuur',
                'question_text' => 'Welk dier bouwt dammen in rivieren?',
                'option_a' => 'Otter',
                'option_b' => 'Eend',
                'option_c' => 'Bever',
                'option_d' => 'Kikker',
                'correct_option' => 'C',
                'points' => 15,
                'sequence' => 4,
            ],
            [
                'name' => 'Bloemen en Bijen',
                'question_text' => 'Waarom zijn bijen zo belangrijk voor bloemen?',
                'option_a' => 'Ze eten onkruid op',
                'option_b' => 'Ze bestuiven de bloemen',
                'option_c' => 'Ze beschermen bloemen tegen rupsen',
                'option_d' => 'Ze geven water aan bloemen',
                'correct_option' => 'B',
                'points' => 15,
                'sequence' => 5,
            ],
        ];

        foreach ($routeStops as $stop) {
            LocationRouteStop::firstOrCreate(
                [
                    'location_id' => $location->id,
                    'sequence' => $stop['sequence'],
                ],
                $stop
            );
        }
    }
}
