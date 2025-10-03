{{-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}




@component('mail::message')
# Bonjour {{ $user->nom }},

Votre compte a été créé sur la plateforme de gestion des stages.

Voici vos identifiants temporaires :

**Email :** {{ $user->email }}
**Mot de passe temporaire :** `{{ $temporaryPassword }}`

👉 Pour des raisons de sécurité, vous devrez **changer ce mot de passe lors de votre première connexion**.

Merci et bienvenue,
L’équipe de gestion des stages

@endcomponent
