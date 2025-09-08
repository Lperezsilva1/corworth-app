<?php

// app/Livewire/Projects/ProjectComments.php
namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\{Project, ProjectComment,ProjectCommentAttachment};
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class ProjectComments extends Component
{
    use WithFileUploads;
    
    public int $projectId;
    public bool $composerOpen = false; // ← nuevo
    public string $commentTitle = '';
    public ?string $commentBody = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[] */
    public array $uploads = []; // múltiples archivos

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
            // Ajusta tipos/size a tu gusto:
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

        // Guardar adjuntos (si hay)
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
            // Al borrar el comentario, por FK cascade se eliminan sus adjuntos y archivos (si deseas, puedes borrar los archivos del disco manualmente)
            $c->delete();
            session()->flash('comment_ok', 'Comment deleted.');
        }
    }

    public function getCommentsProperty()
    {
        return ProjectComment::with(['user','attachments'])  // 👈 carga adjuntos
            ->where('project_id', $this->projectId)
            ->latest()
            ->take(50)
            ->get();
    }

    public function render()
    {
        return view('livewire.projects.project-comments');
    }
}