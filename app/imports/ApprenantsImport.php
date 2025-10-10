<?php

namespace App\Imports;

use App\Models\Apprenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;

class ApprenantsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        // Debug : voir ce qui est reçu
        Log::info('Ligne importée:', $row);

        // Vérifier si l'email ou le matricule existe déjà
        $existingApprenant = Apprenant::where('email', $row['email'])
            ->orWhere('matricule', $row['matricule'])
            ->first();

        if ($existingApprenant) {
            Log::warning("Apprenant déjà existant: {$row['email']}");
            return null;
        }

        // Nettoyer les données
        $matricule = trim($row['matricule']);
        $metier_id = (int)$row['metier_id'];

        Log::info("Création apprenant - Matricule: {$matricule}, Metier ID: {$metier_id}");

        return new Apprenant([
            'nom' => trim($row['nom']),
            'prenom' => trim($row['prenom']),
            'email' => trim($row['email']),
            'matricule' => $matricule,
            'password' => Hash::make($matricule), // Le matricule comme mot de passe par défaut
            'metier_id' => $metier_id,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:apprenants,email',
            'matricule' => 'required|string|unique:apprenants,matricule',
            'metier_id' => 'required|integer|exists:metiers,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email existe déjà',
            'email.email' => 'L\'email n\'est pas valide',
            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Ce matricule existe déjà',
            'metier_id.required' => 'Le métier est obligatoire',
            'metier_id.exists' => 'Ce métier n\'existe pas',
        ];
    }
}
