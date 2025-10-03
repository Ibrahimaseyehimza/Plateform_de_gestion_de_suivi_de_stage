<?php

namespace Database\Seeders;

use App\Models\RH;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //      RH::create([
    //     'nom' => 'Ba',
    //     'prenom' => 'Awa',
    //     'email' => 'awa.ba@techsolutions.sn',
    //     'entreprise_id' => 1,
    // ]);

         $rhs = [
            [
                'nom' => 'Sy',
                'prenom' => 'Khady',
                'email' => 'khady@gmail.sn',
                'entreprise_id' => 1, // Sonatel
            ],
            [
                'nom' => 'Diop',
                'prenom' => 'Awa',
                'email' => 'awa@orange.sn',
                'entreprise_id' => 2, // Orange Sénégal
            ],
            [
                'nom' => 'Mbaye',
                'prenom' => 'Mamadou',
                'email' => 'mamadou.mbaye@expresso.sn',
                'entreprise_id' => 3, // Expresso Sénégal
            ],
            [
                'nom' => 'Cisse',
                'prenom' => 'Aminata',
                'email' => 'aminata.cisse@sgbs.sn',
                'entreprise_id' => 4, // SGBS
            ],
            [
                'nom' => 'Ndao',
                'prenom' => 'Modou',
                'email' => 'modou.ndao@senelec.sn',
                'entreprise_id' => 5, // Senelec
            ],
            [
                'nom' => 'Thiam',
                'prenom' => 'Bineta',
                'email' => 'bineta.thiam@atos.sn',
                'entreprise_id' => 6, // ATOS Sénégal
            ],
            [
                'nom' => 'Kane',
                'prenom' => 'Cheikh',
                'email' => 'cheikh.kane@teyliom.com',
                'entreprise_id' => 7, // Teyliom Group
            ],
            [
                'nom' => 'Seck',
                'prenom' => 'Maimouna',
                'email' => 'maimouna.seck@intouch.sn',
                'entreprise_id' => 8, // InTouch Sénégal
            ],
        ];

        foreach ($rhs as $rh) {
            RH::create($rh);
        }
    }
}
