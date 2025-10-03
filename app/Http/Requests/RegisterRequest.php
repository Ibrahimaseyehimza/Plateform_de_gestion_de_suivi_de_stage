<?php

// namespace App\Http\Requests;

// use App\Models\User;
// use Illuminate\Foundation\Http\FormRequest;

// class RegisterRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function rules(): array
//     {
//         return [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|unique:users,email',
//             'password' => 'required|string|min:6|confirmed',
//             // 'role' => 'required|in:chef_departement,chef_metier,maitre_stage,rh,apprenant',
//             'role' => 'required|in:' . implode(',', [
//                 User::ROLE_CHEF_DEPARTEMENT,
//                 User::ROLE_CHEF_METIER,
//                 User::ROLE_MAITRE_STAGE,
//                 User::ROLE_RH,
//                 User::ROLE_APPRENANT,
//             ]),
//             // 'departement_id' => 'nullable|exists:metiers,id',

//             'invitation_key' => 'required|string',

//             // 'metier_id' => 'nullable|exists:metiers,id',

//             // 'departement_id' => 'nullable|exists:metiers,id',
//         ];


//         if ($this->role === User::ROLE_APPRENANT || $this->role === User::ROLE_CHEF_METIER) {
//             $rules['metier_id'] = 'required|exists:metiers,id';
//         }

//         // if ($this->role === User::ROLE_CHEF_DEPARTEMENT) {
//         //     $rules['departement_id'] = 'required|exists:departements,id';
//         // }

//         if ($this->role === User::ROLE_RH || $this->role === User::ROLE_MAITRE_STAGE) {
//             $rules['entreprise_id'] = 'required|exists:entreprises,id';
//         }



//         return $rules;
//     }
//      /**
//      * Get custom messages for validator errors.
//      */
//     public function messages(): array
//     {
//         return [
//             'name.required' => 'Le nom est obligatoire.',
//             'name.string' => 'Le nom doit être une chaîne de caractères.',
//             'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

//             'email.required' => 'L\'email est obligatoire.',
//             'email.email' => 'L\'email doit être une adresse email valide.',
//             'email.unique' => 'Cet email est déjà utilisé.',

//             'password.required' => 'Le mot de passe est obligatoire.',
//             'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
//             'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',

//             'password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
//             'password_confirmation.min' => 'La confirmation du mot de passe doit contenir au moins 8 caractères.',

//             'role.in' => 'Le rôle doit être chef de departement, chef de metier, ou apprenant',
//         ];
//     }



// }
























// namespace App\Http\Requests;

// use App\Models\User;
// use Illuminate\Foundation\Http\FormRequest;

// class RegisterRequest extends FormRequest
// {
//     public function authorize(): bool
//     {
//         return true;
//     }

//     public function rules(): array
//     {
//         // 🔹 Règles de base
//         $rules = [
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|unique:users,email',
//             'password' => 'required|string|min:6|confirmed',
//             'role' => 'required|in:' . implode(',', [
//                 User::ROLE_CHEF_DEPARTEMENT,
//                 User::ROLE_CHEF_METIER,
//                 User::ROLE_MAITRE_STAGE,
//                 User::ROLE_RH,
//                 User::ROLE_APPRENANT,
//             ]),
//             'invitation_key' => 'required|string',
//         ];

//         // 🔹 Cas spécifiques selon le rôle
//         if ($this->role === User::ROLE_APPRENANT || $this->role === User::ROLE_CHEF_METIER) {
//             $rules['metier_id'] = 'required|exists:metiers,id';
//             $rules['departement_id'] = 'required|exists:departements,id';
//         }

//         if ($this->role === User::ROLE_CHEF_DEPARTEMENT) {
//             $rules['departement_id'] = 'required|exists:departements,id';
//         }

//         if ($this->role === User::ROLE_RH || $this->role === User::ROLE_MAITRE_STAGE) {
//             $rules['entreprise_id'] = 'required|exists:entreprises,id';
//         }

//         return $rules;
//     }

//     public function messages(): array
//     {
//         return [
//             'name.required' => 'Le nom est obligatoire.',
//             'email.required' => 'L’email est obligatoire.',
//             'email.email' => 'L’email doit être une adresse valide.',
//             'email.unique' => 'Cet email est déjà utilisé.',
//             'password.required' => 'Le mot de passe est obligatoire.',
//             'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
//             'role.required' => 'Le rôle est obligatoire.',
//             'role.in' => 'Le rôle choisi est invalide.',
//             'departement_id.required' => 'Le département est obligatoire pour ce rôle.',
//             'departement_id.exists' => 'Le département sélectionné n’existe pas.',
//             'metier_id.required' => 'Le métier est obligatoire pour ce rôle.',
//             'metier_id.exists' => 'Le métier sélectionné n’existe pas.',
//             'entreprise_id.required' => 'L’entreprise est obligatoire pour ce rôle.',
//             'entreprise_id.exists' => 'L’entreprise sélectionnée n’existe pas.',
//             'invitation_key.required' => 'La clé d’invitation est obligatoire.',
//         ];
//     }
// }
















namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 🔹 Règles de base
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:' . implode(',', [
                User::ROLE_CHEF_DEPARTEMENT,
                User::ROLE_CHEF_METIER,
                User::ROLE_MAITRE_STAGE,
                User::ROLE_RH,
                User::ROLE_APPRENANT,
            ]),
            'invitation_key' => 'required|string',
        ];

        // 🔹 Cas spécifiques selon le rôle
        if ($this->role === User::ROLE_APPRENANT || $this->role === User::ROLE_CHEF_METIER) {
            $rules['metier_id'] = 'required|exists:metiers,id';
            $rules['departement_id'] = 'required|exists:departements,id';
        }

        // if ($this->role === User::ROLE_CHEF_DEPARTEMENT) {
        //     $rules['departement_id'] = 'required|exists:departements,id';
        // }

        if ($this->role === User::ROLE_RH || $this->role === User::ROLE_MAITRE_STAGE) {
            $rules['entreprise_id'] = 'required|exists:entreprises,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L’email est obligatoire.',
            'email.email' => 'L’email doit être une adresse valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle choisi est invalide.',
            'departement_id.required' => 'Le département est obligatoire pour ce rôle.',
            'departement_id.exists' => 'Le département sélectionné n’existe pas.',
            'metier_id.required' => 'Le métier est obligatoire pour ce rôle.',
            'metier_id.exists' => 'Le métier sélectionné n’existe pas.',
            'entreprise_id.required' => 'L’entreprise est obligatoire pour ce rôle.',
            'entreprise_id.exists' => 'L’entreprise sélectionnée n’existe pas.',
            'invitation_key.required' => 'La clé d’invitation est obligatoire.',
        ];
    }

    /**
     * 🔹 Validation personnalisée après les règles
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier unicité du chef de département
            if ($this->role === User::ROLE_CHEF_DEPARTEMENT) {
                $exists = User::where('role', User::ROLE_CHEF_DEPARTEMENT)
                    ->where('departement_id', $this->departement_id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('departement_id', 'Ce département a déjà un chef de département.');
                }
            }

            // Vérifier unicité du chef de métier
            if ($this->role === User::ROLE_CHEF_METIER) {
                $exists = User::where('role', User::ROLE_CHEF_METIER)
                    ->where('metier_id', $this->metier_id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('metier_id', 'Ce métier a déjà un chef de métier.');
                }
            }
        });
    }
}
