<div class="p-8 space-y-8 w-full">

  <!-- KPIs (4) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($cards as $c)
      @php
        $title = (string)($c['title'] ?? '');
        $value = (string)($c['value'] ?? '—');
        $delta = (string)($c['delta'] ?? '');
        // Permite pasar $c['icon'] => 'projects'|'clock'|'check'|'bolt'|'trending-up'|'users'|'alert'|'files'|'chart'
        $icon  = (string)($c['icon'] ?? '');

        // Auto-choose icon if none provided
       if ($icon === '') {
    $t = strtolower($title);

    $icon = match (true) {
        str_contains($t, 'pending'), str_contains($t, 'await')        => 'clock',
        str_contains($t, 'working'), str_contains($t, 'in progress')  => 'bolt',
        str_contains($t, 'approved'), str_contains($t, 'complete')    => 'check',
        str_contains($t, 'cancel')                                    => 'alert',
        str_contains($t, 'project')                                   => 'projects',
        str_contains($t, 'user'), str_contains($t, 'member')          => 'users',
        str_contains($t, 'revenue'), str_contains($t, 'sales')        => 'trending-up',
        default                                                       => 'chart',
    };
}

        // Render inline SVG by key
        $iconSvg = match($icon) {
          'projects' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h18M3 12h18M3 17h18"/><path d="M7 7v10"/></svg>',
          'clock'    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/></svg>',
          'check'    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
          'bolt'     => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>',
          'trending-up' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 7-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4h7v7"/></svg>',
          'users'    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
          'files'    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8"/><path d="M14 2v6h6"/><path d="M20 8v10a2 2 0 0 1-2 2h-8"/></svg>',
          'alert'    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
          default    => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 13h3v5H7zM12 8h3v10h-3zM17 11h3v7h-3z"/></svg>',
        };

        $deltaClass = str_starts_with($delta, '-') ? 'text-error' : 'text-success';
      @endphp

      <div class="card bg-base-100 border border-base-200 hover:shadow transition">
        <div class="card-body p-5">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <div class="text-sm text-base-content/60 truncate">{{ $title }}</div>
              <div class="mt-1 text-3xl font-bold">{{ $value }}</div>
              <div class="mt-0.5 text-sm {{ $deltaClass }}">{{ $delta }}</div>
            </div>
            <div class="shrink-0">
              <div class="w-10 h-10 rounded-xl bg-base-200 border border-base-300 flex items-center justify-center text-base-content/70">
                {!! $iconSvg !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Row: Tabla (2/3) + Panel lateral (1/3) -->
  <div class="grid grid-cols-1 xl:grid-cols-3 2xl:grid-cols-4 gap-6">

    <!-- Tabla Proyectos Pendientes (layout del screenshot) -->
    <!-- Pending Projects (pretty table) -->
<div class="xl:col-span-2 2xl:col-span-3 card bg-base-100 border border-base-200 shadow-sm">
  <div class="card-body p-0">
    <!-- Card header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-base-200">
      <div>
        <div class="font-semibold">Pending Projects</div>
        <div class="text-xs text-base-content/60">Top 12 by recent activity</div>
      </div>
      @if (Route::has('projects.index'))
        <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-xs">View all</a>
      @endif
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="table w-full text-[15px]">
        <thead class="sticky top-0 z-10 bg-base-100/95 backdrop-blur border-b border-base-200">
          <tr class="text-xs text-base-content/60">
            <th class="w-12 px-6 py-3">
              <label><input type="checkbox" class="checkbox checkbox-sm" /></label>
            </th>
            <th class="w-12 px-2 py-3">#</th>
            <th class="min-w-[260px] px-2 py-3">PROJECT</th>
         
            <th class="min-w-[220px] px-2 py-3">PHASE 1</th>
            <th class="min-w-[220px] px-2 py-3">FULL SET</th>
            <th class="min-w-[160px] px-2 py-3">GENERAL</th>
          </tr>
        </thead>

        <tbody class="[&>tr]:even:bg-base-200/20">
          @forelse(($pendingProjects ?? []) as $p)
            <tr class="align-top hover:bg-base-200/40 transition-colors">
              <!-- checkbox -->
              <td class="px-6 py-4">
                <label><input type="checkbox" class="checkbox checkbox-sm" /></label>
              </td>

              <!-- index -->
              <td class="px-2 py-4">
                <span class="badge badge-ghost badge-sm">{{ $p['idx'] }}</span>
              </td>

              <!-- PROJECT -->
              <td class="px-2 py-4">
                <div class="flex flex-col">
                  @if (Route::has('projects.show'))
                    <a href="{{ route('projects.show', $p['id']) }}"
                       class="link link-hover font-medium">
                      {{ $p['name'] ?? '—' }}
                    </a>
                  @else
                    <span class="font-medium">{{ $p['name'] ?? '—' }}</span>
                  @endif
                  <span class="text-xs text-base-content/60">{{ $p['building'] ?? '—' }}</span>
                </div>
              </td>

          

              <!-- PHASE 1 -->
              <td class="px-2 py-4">
                <div class="flex flex-col gap-1">
                  <span class="truncate">{{ $p['p1_drafter'] ?? '—' }}</span>
                  <x-status-line :key="$p['p1_key'] ?? null" :label="$p['p1_label'] ?? null"/>
                </div>
              </td>

              <!-- FULL SET -->
              <td class="px-2 py-4">
                <div class="flex flex-col gap-1">
                  <span class="truncate">{{ $p['fs_drafter'] ?? '—' }}</span>
                  <x-status-line :key="$p['fs_key'] ?? null" :label="$p['fs_label'] ?? null"/>
                </div>
              </td>

              <!-- GENERAL -->
              <td class="px-2 py-4">
                <div class="flex">
                  <x-status-pill :key="$p['gen_key'] ?? null" :label="$p['gen_label'] ?? null" />
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-12 text-center text-base-content/60">
                No pending projects.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Card footer (optional) -->
    <div class="px-6 py-3 border-t border-base-200 text-xs text-base-content/60 flex items-center justify-between">
      <span>Showing {{ count($pendingProjects ?? []) }} projects</span>
      @if (Route::has('projects.index'))
        <a href="{{ route('projects.index') }}" class="link link-hover">Open projects list</a>
      @endif
    </div>
  </div>
</div>


    {{-- ===== Recent Activity ===== --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm">
      <div class="card-body p-6 space-y-6">
        <div class="flex items-start justify-between">
          <div>
            <div class="font-semibold">Recent Activity</div>
            <div class="text-xs text-base-content/60">
              Latest project updates • {{ now()->format('M d, Y') }}
            </div>
          </div>
        </div>

        {{-- Timeline --}}
        <div class="relative">
          <div class="absolute left-[11px] top-2 bottom-2 w-px bg-base-300/70"></div>

          <div class="space-y-4">
            @forelse(($activity ?? []) as $a)
              <div class="relative pl-8">
                {{-- dot/icon --}}
                <div class="absolute left-0 top-[2px]">
                  <div class="w-5 h-5 rounded-full bg-base-200 flex items-center justify-center">
                    <span class="{{ $a['color'] }} text-[11px] leading-none">{{ $a['icon'] }}</span>
                  </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <div class="text-sm font-medium truncate">
                      @if (Route::has('projects.show') && ($a['projectId'] ?? null))
                        <a href="{{ route('projects.show', $a['projectId']) }}" class="link link-hover">
                          {{ $a['title'] }} — {{ $a['project'] ?? 'Project' }}
                        </a>
                      @else
                        {{ $a['title'] }} — {{ $a['project'] ?? 'Project' }}
                      @endif
                    </div>
                    @if(!empty($a['body']))
                      <div class="text-xs text-base-content/70 line-clamp-2">
                        {{ $a['body'] }}
                      </div>
                    @endif
                    <div class="mt-1 text-[11px] text-base-content/50">
                      {{ $a['user'] ?? 'System' }} • {{ $a['when'] ?? '' }}
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div class="text-sm text-base-content/60">No recent activity.</div>
            @endforelse
          </div>
        </div>

        <div class="flex items-center justify-end">
          @if (Route::has('projects.index'))
            <a href="{{ route('activity.index') }}" class="btn btn-ghost btn-sm">View all activity</a>
          @endif
        </div>
      </div>
    </div>

  </div>
</div>
