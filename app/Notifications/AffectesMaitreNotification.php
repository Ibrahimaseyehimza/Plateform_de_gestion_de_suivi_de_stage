<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffectesMaitreNotification extends Notification
{
    use Queueable;


    protected $etudiants;
    protected $entreprise;

    /**
     * Create a new notification instance.
     */

    public function __construct($etudiants, $entreprise)
    {
        $this->etudiants = $etudiants;
        $this->entreprise = $entreprise;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    // public function via(object $notifiable): array
    // {
    //     return ['mail'];
    // }

     public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

    // public function toArray($notifiable)
    // {
    //     return [
    //         'titre' => '👨‍🏫 Nouveaux étudiants à superviser',
    //         'message' => "Vous avez reçu la liste des étudiants affectés dans votre entreprise : {$this->entreprise->nom}",
    //         'entreprise' => $this->entreprise->nom,
    //         'etudiants' => $this->etudiants->pluck('name')->toArray(),
    //         'type' => 'affectation_maitre'
    //     ];
    // }

       public function toDatabase($notifiable)
    {
        return [
            'titre' => '📋 Nouvelle liste d’étudiants à superviser',
            'message' => 'Vous avez reçu une nouvelle liste d’étudiants à encadrer dans l’entreprise ' . $this->entreprise->nom,
            'type' => 'affectation_maitre_stage',
            'etudiants' => $this->etudiants->map(function ($etudiant) {
                return [
                    'id' => $etudiant->id,
                    'nom' => $etudiant->name . ' ' . $etudiant->prenom,
                    'email' => $etudiant->email,
                ];
            }),
        ];
    }
}
