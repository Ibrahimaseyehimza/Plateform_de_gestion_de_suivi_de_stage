<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleCampagneCreee extends Notification
{
    use Queueable;

    protected $campagne;

    /**
     * Create a new notification instance.
     */
    public function __construct($campagne)
    {
        $this->campagne = $campagne;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('Nouvelle campagne de stage créée')
            ->greeting('Bonjour ' . $notifiable->name . ' 👋')
            ->line('Une nouvelle campagne de stage a été créée par le chef de département.')
            ->line('Titre : ' . $this->campagne->titre)
            ->line('Date de début : ' . $this->campagne->date_debut)
            ->line('Date de fin : ' . $this->campagne->date_fin)
            ->action('Voir la campagne', url('/campagnes/' . $this->campagne->id))
            ->line('Merci de votre collaboration.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
