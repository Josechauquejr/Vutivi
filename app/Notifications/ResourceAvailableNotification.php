<?php

namespace App\Notifications;

use App\Models\Resource;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Resource $resource) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'resource_id'    => $this->resource->id,
            'resource_title' => $this->resource->title,
            'message'        => "'{$this->resource->title}' está disponível para empréstimo.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recurso disponível: ' . $this->resource->title)
            ->line("O recurso '{$this->resource->title}' que estava na sua fila de espera está agora disponível.")
            ->action('Ver recurso', route('resources.show', $this->resource->id));
    }
}
