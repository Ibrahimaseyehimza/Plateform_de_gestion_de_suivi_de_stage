<?php

namespace App\Mail;

use App\Models\User;
use App\Models\CampagneDeStage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampagneNotificationRHMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rh;
    public $campagne;

    public function __construct(User $rh, CampagneDeStage $campagne)
    {
        $this->rh = $rh;
        $this->campagne = $campagne;
    }

    public function build()
    {
        return $this->subject('📢 Nouvelle campagne de stage à valider')
            ->markdown('emails.rh.notification');
    }
}

