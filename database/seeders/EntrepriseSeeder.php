<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //      Entreprise::create([
    //     'nom' => 'Tech Solutions',
    //     'adresse' => 'Zone Industrielle, Thiès',
    //     'email' => 'contact@techsolutions.sn',
    //     'telephone' => '77 123 45 67',
    // ]);

    $entreprises = [
            [
                'nom' => 'Sonatel',
                'adresse' => '46 Boulevard de la République, Dakar',
                'email' => 'contact@sonatel.sn',
                'telephone' => '+221 33 839 1010',
                'latitude' => 14.6937,
                'longitude' => -17.4441,
                'metier_id' => 1,
            ],
            [
                'nom' => 'Orange Sénégal',
                'adresse' => 'Route de Ouakam, Dakar',
                'email' => 'info@orange.sn',
                'telephone' => '+221 33 869 6000',
                'latitude' => 14.7167,
                'longitude' => -17.4677,
                'metier_id' => 2,
            ],
            [
                'nom' => 'Expresso Sénégal',
                'adresse' => 'VDN, Dakar',
                'email' => 'contact@expresso.sn',
                'telephone' => '+221 33 849 9000',
                'latitude' => 14.7319,
                'longitude' => -17.4572,
                'metier_id' => 3,
            ],
            [
                'nom' => 'SGBS (Société Générale de Banques au Sénégal)',
                'adresse' => '19 Avenue Léopold Sédar Senghor, Dakar',
                'email' => 'sgbs@sgbs.sn',
                'telephone' => '+221 33 839 3939',
                'latitude' => 14.6729,
                'longitude' => -17.4266,
                'metier_id' => 1,
            ],
            [
                'nom' => 'Senelec',
                'adresse' => 'Rue Elhadji Amadou Assane Ndoye, Dakar',
                'email' => 'contact@senelec.sn',
                'telephone' => '+221 33 839 3131',
                'latitude' => 14.6850,
                'longitude' => -17.4431,
                'metier_id' => 2,
            ],
            [
                'nom' => 'ATOS Sénégal',
                'adresse' => 'Almadies, Dakar',
                'email' => 'contact@atos.sn',
                'telephone' => '+221 33 869 5500',
                'latitude' => 14.7458,
                'longitude' => -17.5020,
                'metier_id' => 3,
            ],
            [
                'nom' => 'Teyliom Group',
                'adresse' => 'Mermoz, Dakar',
                'email' => 'info@teyliom.com',
                'telephone' => '+221 33 869 7000',
                'latitude' => 14.7206,
                'longitude' => -17.4594,
                'metier_id' => 1,
            ],
            [
                'nom' => 'InTouch Sénégal',
                'adresse' => 'Liberté 6, Dakar',
                'email' => 'contact@intouch.sn',
                'telephone' => '+221 33 824 5000',
                'latitude' => 14.7083,
                'longitude' => -17.4625,
                'metier_id' => 2,
            ],
        ];

        foreach ($entreprises as $entreprise) {
            Entreprise::create($entreprise);
        }
    }

}
