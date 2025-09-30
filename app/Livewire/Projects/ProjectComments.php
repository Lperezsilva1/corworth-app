<?php

namespace App\Livewire\Projects;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

use App\Models\{
    Project,
    ProjectComment,
    ProjectCommentAttachment,
    Drafter,
    Building,
    Seller,
    ProjectStatus
};
use App\Notifications\CommentAddedNotification;

class ProjectComments extends Component
{
    use WithFileUploads;

    public int $projectId;
    public bool $composerOpen = false;
    public string $commentTitle = '';
    public ?string $commentBody = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $uploads = [];

    public int $refreshTick = 0;

    public function mount(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function rules(): array
    {
        return [
            'commentTitle' => ['required','string','max:255'],
            'commentBody'  => ['nullable','string','max:2000'],
            'uploads.*'    => ['file','max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,zip,txt'],
        ];
    }

    public function addComment(): void
    {
        $this->validate();

        $comment = ProjectComment::create([
            'project_id' => $this->projectId,
            'user_id'    => Auth::id(),
            'title'      => trim($this->commentTitle),
            'body'       => $this->commentBody && trim($this->commentBody) !== '' ? trim($this->commentBody) : null,
            'is_system'  => false,
            'source'     => 'UI',
        ]);

        // Adjuntos
        foreach ($this->uploads as $file) {
            $path = $file->store("comments/{$comment->id}", 'public');

            ProjectCommentAttachment::create([
                'project_comment_id' => $comment->id,
                'user_id'            => Auth::id(),
                'original_name'      => $file->getClientOriginalName(),
                'disk'               => 'public',
                'path'               => $path,
                'size'               => $file->getSize(),
                'mime'               => $file->getMimeType(),
            ]);
        }

        /** Notificaciones (todos menos el autor) */
        $recipients = \App\Models\User::whereKeyNot(Auth::id())->get();
        $project    = Project::find($this->projectId);

        foreach ($recipients as $user) {
            $user->notify(new CommentAddedNotification(
                projectId:    $this->projectId,
                projectName:  $project?->project_name ?? 'Project',
                commentTitle: $comment->title ?? '',
                commentBody:  $comment->body ?? null,
                commentId:    $comment->id,
                actorId:      Auth::id(),
                actorName:    Auth::user()?->name
            ));
        }

        // Reset + feedback + scroll
        $this->reset(['commentTitle','commentBody','uploads']);
        $this->composerOpen = false;
        $this->dispatch('comment-added');
        session()->flash('comment_ok', 'Comment added.');
    }

    public function deleteComment(int $id): void
    {
        $c = ProjectComment::where('project_id', $this->projectId)->findOrFail($id);
        if (Auth::id() && $c->user_id === Auth::id()) {
            $c->delete();
            session()->flash('comment_ok', 'Comment deleted.');
        }
    }

    public function getCommentsProperty()
    {
        return ProjectComment::with(['user','attachments'])
            ->where('project_id', $this->projectId)
            ->latest()
            ->take(50)
            ->get();
    }

    /**
     * Presenta el cuerpo de comentarios automáticos:
     * - Reemplaza IDs por nombres/etiquetas legibles
     * - Quita el prefijo [AUTO]
     */
    public function presentCommentBody(ProjectComment $comment): ?string
    {
        $body = trim((string) $comment->body);
        if ($body === '') return null;

        $isAuto = ($comment->is_system ?? false) || \Illuminate\Support\Str::startsWith($body, '[AUTO]');
        if (!$isAuto) {
            return $body; // comentario normal
        }

        // Limpia prefijo
        $body = \Illuminate\Support\Str::of($body)->replaceFirst('[AUTO] ', '')->value();

        // Resolutores campo → etiqueta
        $resolvers = [
            'phase1_drafter_id'  => fn($id) => Drafter::find($id)?->name_drafter ?? "ID {$id}",
            'fullset_drafter_id' => fn($id) => Drafter::find($id)?->name_drafter ?? "ID {$id}",
            'building_id'        => fn($id) => Building::find($id)?->name_building ?? "ID {$id}",
            'seller_id'          => fn($id) => Seller::find($id)?->name_seller ?? "ID {$id}",
            'general_status'     => function ($val) {
                if (is_numeric($val)) {
                    return ProjectStatus::find($val)?->label ?? "ID {$val}";
                }
                $st = ProjectStatus::where('key', (string)$val)->first();
                return $st?->label ?? (string)$val;
            },
        ];

        // "- field: old → new"
        $lines = preg_split('/\r\n|\r|\n/', $body);
        $out   = [];

        foreach ($lines as $line) {
            if (preg_match('/^\s*-\s*([a-zA-Z0-9_]+)\s*:\s*(.*?)\s*→\s*(.*?)\s*$/u', $line, $m)) {
                [$_, $field, $old, $new] = $m;

                $resolve = $resolvers[$field] ?? null;
                $oldTrim = trim($old);
                $newTrim = trim($new);
                $oldIsEmpty = ($oldTrim === '' || $oldTrim === '—' || strtolower($oldTrim) === 'null');
                $newIsEmpty = ($newTrim === '' || $newTrim === '—' || strtolower($newTrim) === 'null');

                if ($resolve) {
                    // numéricos
                    if (!$oldIsEmpty && is_numeric($oldTrim)) $old = $resolve((int) $oldTrim);
                    if (!$newIsEmpty && is_numeric($newTrim)) $new = $resolve((int) $newTrim);
                    // claves string en general_status
                    if ($field === 'general_status') {
                        if (!$oldIsEmpty && !is_numeric($oldTrim)) $old = $resolve($oldTrim);
                        if (!$newIsEmpty && !is_numeric($newTrim)) $new = $resolve($newTrim);
                    }
                }

                $out[] = "- {$field}: " . ($oldIsEmpty ? '—' : $old) . " → " . ($newIsEmpty ? '—' : $new);
            } else {
                $out[] = $line;
            }
        }

        return implode("\n", $out);
    }

    public function render()
    {
        return view('livewire.projects.project-comments');
    }
}
