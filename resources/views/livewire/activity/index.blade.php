<div class="p-6">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
        ['label' => 'Activity']
      ]" />
      <div>
        <flux:heading size="xl" level="1">Activity</flux:heading>
        <flux:subheading size="lg" class="mb-6">
          Recent changes and comments on projects
        </flux:subheading>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <!-- Date range (optional) -->
      <input type="date" class="input input-bordered w-40" wire:model.live="from" />
      <input type="date" class="input input-bordered w-40" wire:model.live="to" />

      <!-- Search -->
      <input
        type="text"
        wire:model.live="search"
        placeholder="Search activity..."
        class="input input-bordered w-64"
      />

      <!-- Per page selector -->
      <select class="select select-bordered w-28" wire:model.live="perPage">
        <option value="10">10 / page</option>
        <option value="15">15 / page</option>
        <option value="25">25 / page</option>
        <option value="50">50 / page</option>
      </select>
    </div>
  </div>

  <flux:separator variant="subtle" />

  <!-- Card with timeline -->
  <div class="card bg-base-100 border border-base-200 shadow-sm">
    <div class="card-body p-6">

      {{-- Timeline container --}}
      <div class="relative">
        {{-- vertical line --}}
        <div class="absolute left-[18px] top-2 bottom-2 w-[2px] bg-base-300/70 rounded-full"></div>

        <div class="space-y-5">
          @forelse($activities as $a)
            @php
              $title = strtolower($a->title ?? '');
              $body  = strtolower($a->body ?? '');

              $isApproval  = str_contains($title, 'approval') || str_contains($body, 'approved');
              $isCancelled = str_contains($title, 'cancel')   || str_contains($body, 'cancelled');
              $isWorking   = str_contains($title, 'working');

              if ($isApproval) {
                $iconBg  = 'bg-success/10 border-success/30 text-success';
                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
              } elseif ($isCancelled) {
                $iconBg  = 'bg-error/10 border-error/30 text-error';
                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
              } elseif ($isWorking) {
                $iconBg  = 'bg-info/10 border-info/30 text-info';
                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';
              } else {
                $iconBg  = 'bg-base-200 border-base-300 text-base-content/60';
                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3h6l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M13 3v4a1 1 0 0 0 1 1h4"/></svg>';
              }
            @endphp

            <div class="relative pl-12">
              {{-- round icon --}}
              <div class="absolute left-0 top-0">
                <div class="w-8 h-8 rounded-full border {{ $iconBg }} flex items-center justify-center shadow-sm">
                  {!! $iconSvg !!}
                </div>
              </div>

              {{-- content --}}
              <div class="min-w-0">
                <div class="text-sm font-semibold leading-5">
                  @if (Route::has('projects.show') && $a->project_id)
                    <a href="{{ route('projects.show', $a->project_id) }}" class="link link-hover">
                      {{ $a->title ?? 'Activity' }} — {{ $a->project?->project_name ?? 'Project' }}
                    </a>
                  @else
                    {{ $a->title ?? 'Activity' }} — {{ $a->project?->project_name ?? 'Project' }}
                  @endif
                </div>

                @if(!empty($a->body))
                  <div class="mt-0.5 text-[13px] leading-5 text-base-content/70">
                    {{ \Illuminate\Support\Str::limit($a->body, 160) }}
                  </div>
                @endif

                <div class="mt-1.5 text-[11px] text-base-content/50">
                  {{ $a->user?->name ?? 'System' }} • {{ $a->created_at?->diffForHumans() }}
                </div>
              </div>
            </div>
          @empty
            <div class="text-sm text-base-content/60">No activity found.</div>
          @endforelse
        </div>
      </div>

      <!-- Pagination -->
      <div class="mt-6">
        {{-- Use plain links(): works with paginate, simplePaginate and cursorPaginate --}}
        {{ $activities->links() }}
      </div>
    </div>
  </div>
</div>
