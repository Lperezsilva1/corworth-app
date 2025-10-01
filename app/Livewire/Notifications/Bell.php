<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class Bell extends Component
{
    public int $limit = 8;

    protected $listeners = [
        'notification-read' => '$refresh',
        'notification-new'  => '$refresh',
    ];

    /** Contador de no leídas para el badge */
    public function getUnreadCountProperty(): int
    {
        return Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    /** Ítems ya mapeados para la vista (view-model) */
    public function getItemsProperty()
    {
        $user = Auth::user();
        if (! $user) return collect();

        $notes = $user->notifications()->latest()->limit($this->limit)->get();

        // Mantén el contador fresco (opcional)
        // $this->unreadCount = $user->unreadNotifications()->count();

        return $notes->map(fn (DatabaseNotification $n) => $this->mapNotification($n));
    }

    /** Mapea cada notificación a un payload listo para pintar en Blade */
    protected function mapNotification(DatabaseNotification $n): array
    {
        $d    = $n->data ?? [];
        $type = $d['type'] ?? '';

        // Tabs permitidos por ahora
        $allowedTabs  = ['general', 'notes'];
        $requestedTab = $d['tab'] ?? null;
        $tab = in_array($requestedTab, $allowedTabs, true)
            ? $requestedTab
            : ($type === 'comment_added' ? 'notes' : 'general');

        $projectId = $d['project_id'] ?? null;

        // Respeta url del payload si viene; si no, construye con tab
        $url = $d['url'] ?? ($projectId
            ? route('projects.show', ['project' => $projectId, 'tab' => $tab])
            : '#');

        // Título / descripción alineados con tu Blade actual
        $title = $type === 'comment_added'
            ? 'New comment on ' . ($d['project_name'] ?? 'project')
            : ($d['title'] ?? 'Notification');

        $desc  = $d['body'] ?? '';
        $actor = $d['actor_name'] ?? null;

        return [
            'id'        => $n->id,
            'is_unread' => is_null($n->read_at),
            'title'     => $title,
            'desc'      => $desc,
            'actor'     => $actor,
            'url'       => $url,
            'dot_class' => is_null($n->read_at) ? 'bg-primary' : 'bg-base-300',
            'created_h' => $n->created_at->diffForHumans(),
        ];
    }

    public function markAsRead(string $notificationId): void
    {
        $n = Auth::user()?->notifications()->find($notificationId);
        if ($n && $n->read_at === null) {
            $n->markAsRead();
            $this->dispatch('notification-read');
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->dispatch('notification-read');
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
