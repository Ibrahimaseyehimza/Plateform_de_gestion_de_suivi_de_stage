<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Apprenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MigrateApprenantsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-apprenants-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apprenants = Apprenant::all();
        $count = 0;

        foreach ($apprenants as $a) {
            // On vérifie que l'email n'existe pas déjà
            if (!User::where('email', $a->email)->exists()) {
                User::create([
                    'name' => $a->name,
                    'prenom' => $a->prenom,
                    'email' => $a->email,
                    'matricule' => $a->matricule,
                    'password' => $a->password ?? Hash::make($a->matricule),
                    'role' => 'apprenant',
                    'metier_id' => $a->metier_id,
                ]);
                $count++;
            }
        }

        $this->info("✅ $count apprenants migrés avec succès !");
        return Command::SUCCESS;
    }
}
