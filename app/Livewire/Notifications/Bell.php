<?php

// app/Livewire/Notifications/Bell.php
namespace App\Livewire\Notifications;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Bell extends Component
{
    public int $limit = 8;

    protected $listeners = ['notification-read' => '$refresh', 'notification-new' => '$refresh'];

    public function getUnreadCountProperty(): int
    {
        return Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    public function getLatestNotificationsProperty()
    {
        return Auth::user()
            ?->notifications()
            ->latest()
            ->limit($this->limit)
            ->get() ?? collect();
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
