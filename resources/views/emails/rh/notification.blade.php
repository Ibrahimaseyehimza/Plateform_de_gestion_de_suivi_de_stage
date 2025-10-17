@component('mail::message')
# Bonjour {{ $rh->nom }},

Une **nouvelle campagne de stage** vient d’être créée par le chef de département.

### 📋 Détails :
- **Titre :** {{ $campagne->titre }}
- **Période :** du {{ \Carbon\Carbon::parse($campagne->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($campagne->date_fin)->format('d/m/Y') }}
- **Métier :** {{ $campagne->metier->nom ?? 'Non spécifié' }}

---

@component('mail::button', ['url' => config('app.url') . '/login'])
👋 Se connecter pour valider la campagne
@endcomponent

> Connectez-vous à votre espace RH avec vos **identifiants habituels** pour accepter ou refuser cette campagne.

Merci pour votre collaboration,
**L’équipe ISEP – Gestion des stages**
@endcomponent
