<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProjectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $action,     // created | updated | deleted
        public Project $project,
        public ?int $actorId,
        public ?string $actorName
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // agrega 'broadcast' o 'mail' si quieres
    }

public function toDatabase($notifiable): array
{
    $titles = [
        'created' => 'New project',
        'updated' => 'Project updated',
        'deleted' => 'Project deleted',
    ];

    $bodies = [
        'created' => "New project created: {$this->project->project_name}",
        'updated' => "Project updated: {$this->project->project_name}",
        'deleted' => "Project deleted: {$this->project->project_name}",
    ];

    return [
        // lo que tu Blade ya usa
        'type'         => "project_{$this->action}",         // ej: project_created
        'title'        => $titles[$this->action] ?? 'Notification',
        'body'         => $bodies[$this->action] ?? 'Project activity',
        'project_id'   => $this->project->id,
        'project_name' => $this->project->project_name ?? "Project #{$this->project->id}",
        'actor_id'     => $this->actorId,
        'actor_name'   => $this->actorName,

        // opcional: tu Blade hoy no lo usa, pero no estorba
        'url'          => $this->action === 'deleted'
                            ? null
                            : route('projects.show', ['project' => $this->project->id, 'tab' => 'general']),
        'action'       => $this->action, // por si luego lo necesitas
    ];
}
}
