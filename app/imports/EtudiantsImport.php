<?php

// namespace App\Imports;

// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;

// class EtudiantsImport implements ToModel, WithHeadingRow
// {
//     public function model(array $row)
//     {
//         return new User([
//             'name' => $row['nom'],
//             'prenom' => $row['prenom'],
//             'email' => $row['email'],
//             'password' => Hash::make($row['matricule']),
//             'matricule' => $row['matricule'],
//             'metier_id' => $row['metier_id'],
//             'role' => 'apprenant',
//         ]);
//     }
// }













namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithValidation;

class EtudiantsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        // Vérifier si l'email existe déjà
        $existingUser = User::where('email', $row['email'])->first();

        if ($existingUser) {
            // Ignorer cette ligne si l'email existe déjà
            return null;
        }

        return new User([
            'name' => $row['nom'],
            'prenom' => $row['prenom'],
            'email' => $row['email'],
            'password' => Hash::make($row['matricule']),
            'matricule' => $row['matricule'],
            'metier_id' => $row['metier_id'],
            'role' => 'apprenant',
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'matricule' => 'required|string|unique:users,matricule',
            'metier_id' => 'required|exists:metiers,id',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => 'Cet email existe déjà',
            'matricule.unique' => 'Ce matricule existe déjà',
        ];
    }
}
