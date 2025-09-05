<?php

// app/Livewire/Projects/ProjectComments.php
namespace App\Livewire\Projects;

use Livewire\Component;
use App\Models\{Project, ProjectComment};
use Illuminate\Support\Facades\Auth;

class ProjectComments extends Component
{
    public int $projectId;
    public string $commentBody = '';
    public int $refreshTick = 0; // <- propiedad dummy

    public function mount(int $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function rules(): array
    {
        return ['commentBody' => ['required','string','max:2000']];
    }

    public function addComment(): void
    {
        $this->validate();

        ProjectComment::create([
            'project_id' => $this->projectId,
            'user_id'    => Auth::id(),
            'body'       => trim($this->commentBody),
        ]);

        $this->reset('commentBody');
        $this->dispatch('comment-added'); // opcional para escuchar refrescos
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
        return ProjectComment::with('user')
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
