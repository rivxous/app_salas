<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservas;

class ReservaCreadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reserva;

    public function __construct(Reservas $reserva)
    {
        $this->reserva = $reserva;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Canales de notificación
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva reserva asignada')
            ->markdown('emails.reserva-creada', [
                'reserva' => $this->reserva,
                'user' => $notifiable
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'nueva_reserva',
            'message' => 'Has sido agregado a la reserva: '.$this->reserva->titulo,
            'reserva_id' => $this->reserva->id,
            'fecha' => now()->toDateTimeString()
        ];
    }
}