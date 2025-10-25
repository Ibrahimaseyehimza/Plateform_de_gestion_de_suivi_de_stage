<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EtudiantAffecteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $affectation;
    protected $campagne;
    protected $entreprise;
    protected $chefMetier;

    /**
     * Create a new notification instance.
     */
    public function __construct($affectation, $campagne, $entreprise, $chefMetier)
    {
        $this->affectation = $affectation;
        $this->campagne = $campagne;
        $this->entreprise = $entreprise;
        $this->chefMetier = $chefMetier;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $dateDebut = date('d/m/Y', strtotime($this->campagne->date_debut));
        $dateFin = date('d/m/Y', strtotime($this->campagne->date_fin));

        return (new MailMessage)
            ->subject('🎉 Félicitations ! Vous êtes affecté(e) en stage')
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ' 👋')
            ->line('Nous avons le plaisir de vous informer que vous avez été affecté(e) à un stage.')
            ->line('')
            ->line('**📋 Détails de votre affectation :**')
            ->line('**Entreprise :** ' . $this->entreprise->nom)
            ->line('**Adresse :** ' . $this->entreprise->adresse)
            ->line('**Téléphone :** ' . $this->entreprise->telephone)
            ->line('**Email :** ' . $this->entreprise->email)
            ->line('')
            ->line('**🎓 Informations sur le stage :**')
            ->line('**Titre :** ' . $this->campagne->titre)
            ->line('**Description :** ' . $this->campagne->description)
            ->line('**Métier :** ' . $this->campagne->metier->nom)
            ->line('**Date de début :** ' . $dateDebut)
            ->line('**Date de fin :** ' . $dateFin)
            ->line('')
            ->line('**👤 Affecté par :** ' . $this->chefMetier->name . ' (Chef de Métier)')
            ->line('')
            ->line('Veuillez vous présenter à l\'entreprise à la date indiquée.')
            ->action('Voir mes détails de stage', url('/stages/mon-stage'))
            ->line('')
            ->line('**⚠️ Important :**')
            ->line('• Apportez vos documents d\'identité')
            ->line('• Soyez ponctuel(le) dès le premier jour')
            ->line('• Contactez l\'entreprise en cas d\'empêchement')
            ->line('')
            ->line('Nous vous souhaitons un excellent stage ! 🚀')
            ->salutation('Cordialement, L\'équipe de gestion des stages');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'affectation_id' => $this->affectation->id,
            'entreprise_id' => $this->entreprise->id,
            'entreprise_nom' => $this->entreprise->nom,
            'campagne_id' => $this->campagne->id,
            'campagne_titre' => $this->campagne->titre,
            'chef_metier' => $this->chefMetier->name,
        ];
    }
}
