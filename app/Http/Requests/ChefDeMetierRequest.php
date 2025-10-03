<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChefDeMetierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'metier_id' => 'required|exists:metiers,id',
        ];

        // Pour les mises à jour (PUT/PATCH), rendre l'email unique sauf pour l'enregistrement actuel
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $chefId = $this->route('chefDeMetier')->id ?? null;
            $rules['email'] .= '|unique:chef_de_metiers,email,' . $chefId;
        } else {
            // Pour les créations (POST)
            $rules['email'] .= '|unique:chef_de_metiers,email';
        }

        return $rules;

    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.max' => 'Le prénom ne peut pas dépasser 255 caractères.',

            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.max' => 'L\'email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cet email est déjà utilisé.',

            'metier_id.required' => 'Le métier est obligatoire.',
            'metier_id.exists' => 'Le métier sélectionné n\'existe pas.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'email',
            'metier_id' => 'métier',
        ];
    }

}
