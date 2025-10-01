<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $projectId,
        public string $projectName,
        public ?int $actorId,
        public ?string $actorName
    )
    {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
  

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'project_created',
            'project_id'   => $this->projectId,
            'project_name' => $this->projectName,
            'created_by'   => $this->actorId,
            'actor_name'   => $this->actorName,
            'url'          => route('projects.show', $this->projectId) . '#comments',
            'message'      => "New project created: {$this->projectName}",
        ];

}
}
