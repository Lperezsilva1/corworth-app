<?php
// app/Notifications/CommentAddedNotification.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // opcional, quita si no usas cola
use Illuminate\Notifications\Notification;

class CommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $projectId,
        public string $projectName,
        public string $commentTitle,
        public ?string $commentBody,
        public int $commentId,
        public ?int $actorId,
        public ?string $actorName
    ) {}

    public function via($notifiable): array
    {
        // Para tiempo real luego: ['database','broadcast']
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'comment_added',
            'project_id'   => $this->projectId,
            'project_name' => $this->projectName,
            'comment_id'   => $this->commentId,
            'title'        => $this->commentTitle,
            'body'         => $this->commentBody,
            'actor_id'     => $this->actorId,
            'actor_name'   => $this->actorName,
            'url'          => route('projects.show', $this->projectId) . '#comments', // ajusta tu ruta
        ];
    }

    // (Opcional) para mail, SMS, etc.
}
