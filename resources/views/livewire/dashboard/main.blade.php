<div class="p-6 font-sans">
  <div class="overflow-x-auto">
    <div class="relative mb-6 w-full">
      {{-- Breadcrumbs --}}
      <x-breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/dashboard')],
      ]" />

      <!-- Encabezado -->
      <div class="flex items-center justify-between">
        <div>
          <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
          <flux:subheading size="lg" class="mb-6">
            {{ __('Overview and pending projects') }}
          </flux:subheading>
        </div>
      </div>
      <flux:separator variant="subtle" />
    </div>

 {{-- ================== KPIs (estilo stats DaisyUI) ================== --}}
@php
  // Orden consistente de cards
  $orderTitles = [
    'Total Projects',
    'Created (in range)',
    'Approved',
    'Open Projects',
    'Awaiting PFS approval',
    'Deviated',
  ];
  if (!empty($cards)) {
    usort($cards, function ($a, $b) use ($orderTitles) {
      $ia = array_search($a['title'] ?? '', $orderTitles, true);
      $ib = array_search($b['title'] ?? '', $orderTitles, true);
      $ia = $ia === false ? PHP_INT_MAX : $ia;
      $ib = $ib === false ? PHP_INT_MAX : $ib;
      return $ia <=> $ib;
    });
  }

  // Mostrar solo 5 KPIs para abarcar el ancho completo
  $top5 = array_slice($cards ?? [], 0, 5);

  // Mapear icono por título (opcional)
  $kpiIcon = function (string $title) {
    $t = strtolower($title);
    return match (true) {
      str_contains($t, 'total')    => 'chart-bar',
      str_contains($t, 'created')  => 'plus',
      str_contains($t, 'approved') => 'check',
      str_contains($t, 'open')     => 'folder-open',
      str_contains($t, 'awaiting') => 'hourglass',
      str_contains($t, 'deviated') => 'warning',
      default                      => 'dot',
    };
  };
@endphp

<div class="mt-6">
  <div class="stats stats-vertical lg:stats-horizontal w-full rounded-xl bg-base-100 border border-base-300 dark:border-base-200 shadow">
    @foreach($top5 as $c)
      @php
        $title = (string)($c['title'] ?? '');
        $value = (string)($c['value'] ?? '—');
        $delta = (string)($c['delta'] ?? '');
        $isNeg = str_starts_with($delta, '-');
        $icon  = $kpiIcon($title);
      @endphp

      <div class="stat px-5 py-4">
        {{-- Título con mini icono (opcional) --}}
        <div class="stat-title flex items-center gap-1.5">
          @if($icon === 'chart-bar')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
              <path d="M3 3v18h18"/><path d="M7 13h3v5H7zM12 8h3v10h-3zM17 11h3v7h-3z"/>
            </svg>
          @elseif($icon === 'plus')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
          @elseif($icon === 'check')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          @elseif($icon === 'folder-open')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path d="M3 7h6l2 2h10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          @elseif($icon === 'hourglass')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path d="M6 2h12M6 22h12M6 2l6 8 6-8M6 22l6-8 6 8"/></svg>
          @elseif($icon === 'warning')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><path d="M12 2L2 20h20L12 2z"/><path d="M12 8v6M12 18h.01"/></svg>
          @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/></svg>
          @endif
          <span>{{ $title }}</span>
        </div>

        {{-- Valor grande --}}
        <div class="stat-value text-3xl sm:text-4xl">{{ $value }}</div>

        {{-- Delta con flecha y color --}}
        @if($delta !== '')
          <div class="stat-desc flex items-center gap-1 {{ $isNeg ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
           
          </div>
        @endif
      </div>
    @endforeach
  </div>
</div>


    {{-- ================== 2 columnas: tabla + actividad ================== --}}
    @php
      // Helper rápido para badges por estado
      $badgeClass = function (?string $keyOrLabel): string {
        $k = strtolower($keyOrLabel ?? '');
        return match (true) {
          str_contains($k, 'approved')           => 'badge-success',
          str_contains($k, 'working')            => 'badge-info',
          str_contains($k, 'pending')            => 'badge-ghost',
          str_contains($k, 'awaiting')           => 'badge-warning',
          str_contains($k, 'deviated')           => 'badge-error',
          str_contains($k, 'cancel')             => 'badge-neutral',
          str_contains($k, 'draft') || $k === '' => 'badge-outline',
          default                                => 'badge-ghost',
        };
      };
    @endphp

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Columna izquierda: Tabla accionable --}}
      <div class="lg:col-span-2">
        <flux:heading size="lg">{{ __('Open Project') }}</flux:heading>

        <div class="mt-4 bg-base-100 border border-base-300 dark:border-base-200 rounded-md shadow-sm">
          <div class="overflow-x-auto max-h-[560px] overflow-y-auto rounded-md">
           <table class="table table-zebra w-full">
        {{-- Encabezado estilo ref: sticky, uppercase, orden chevron --}}
        <thead class="sticky top-0 z-10 bg-base-200/70 backdrop-blur supports-[backdrop-filter]:backdrop-saturate-150">
          <tr class="text-xs uppercase tracking-wide text-base-content/70">
            <th class="w-10">
             
            </th>
            <th class="w-12">#</th>

            <th class="min-w-[220px]">
              <div class="inline-flex items-center gap-1">
                {{ __('Project') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                  <path d="M7 10l5-5 5 5M7 14l5 5 5-5"/>
                </svg>
              </div>
            </th>

            <th class="min-w-[160px]">
              <div class="inline-flex items-center gap-1">
                {{ __('Seller') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                  <path d="M7 10l5-5 5 5M7 14l5 5 5-5"/>
                </svg>
              </div>
            </th>

            <th class="w-40">{{ __('Status') }}</th>
           
            
          </tr>
        </thead>

        {{-- Cuerpo --}}
        <tbody>
          @forelse($pendingProjects as $p)
            @php
              $rowUrl   = url('/projects/'.($p['id'] ?? ''));
              $idx      = $p['idx'] ?? $loop->iteration;
              $name     = $p['name'] ?? '—';
              $seller   = $p['seller'] ?? '—';
              $updated  = $p['updated'] ?? '—';
              $building = $p['building'] ?? null;
              $label    = $p['gen_label'] ?? $p['gen_key'] ?? 'Pending';
              // usa tu helper de badges si lo tienes:
              $badgeCls = isset($badgeClass) ? $badgeClass($label) : 'badge-ghost';
            @endphp

            <tr class="align-middle hover cursor-pointer" onclick="window.location='{{ $rowUrl }}'">
              <td>
                <label><input type="checkbox" class="checkbox checkbox-sm" onclick="event.stopPropagation()"/></label>
              </td>

              <td class="text-base-content/70">{{ $idx }}</td>

              <td class="min-w-0">
                <div class="font-medium text-base-content truncate">{{ $name }}</div>
                @if($building)
                  <div class="text-xs text-base-content/60 truncate">{{ $building }}</div>
                @endif
              </td>

              <td class="text-sm text-base-content/80 truncate">{{ $seller }}</td>

              <td>
                <span class="badge badge-sm {{ $badgeCls }} rounded-md whitespace-nowrap">
                  {{ $label }}
                </span>
              </td>

          

              
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-10 text-center text-base-content/60">No projects</td>
            </tr>
          @endforelse
        </tbody>
      </table>
          </div>
        </div>
      </div>

      {{-- Columna derecha: Actividad reciente (compacta) --}}
      <div>
        <flux:heading size="lg">{{ __('Recent Activity') }}</flux:heading>

        <div class="bg-base-100 border border-base-300 dark:border-base-200 rounded-md p-3 mt-4 shadow-sm">
          @if(empty($activity))
            <div class="flex items-center gap-2 text-sm text-base-content/70">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="2">
                <path d="M12 8v4m0 4h.01"/><circle cx="12" cy="12" r="9"/>
              </svg>
              <span>{{ __('No recent activity') }}</span>
            </div>
          @else
            <ul class="divide-y divide-base-300 dark:divide-base-200">
              @foreach($activity as $a)
                <li class="py-2 flex items-start gap-2">
                  {{-- Icono --}}
                  <div class="shrink-0 text-lg leading-none">
     <svg xmlns="http://www.w3.org/2000/svg" 
         class="w-5 h-5 stroke-current" 
         fill="none" viewBox="0 0 24 24" 
         stroke-width="2">
        <path d="M12 6v6l4 2"/>
        <circle cx="12" cy="12" r="9"/>
    </svg>
</div>

                  {{-- Texto --}}
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <p class="font-medium text-sm text-base-content truncate">
                        {{ $a['title'] ?? 'Activity' }}
                      </p>
                      <span class="text-xs text-base-content/60 whitespace-nowrap">
                        {{ $a['when'] ?? '' }}
                      </span>
                    </div>

                    @if(!empty($a['body']))
                      <p class="text-xs text-base-content/70 truncate">
                        {{ $a['body'] }}
                      </p>
                    @endif

                    <div class="mt-1 text-xs text-base-content/60 flex items-center gap-1">
                      <span>{{ $a['user'] ?? 'System' }}</span>
                      @if(!empty($a['project']))
                        • <span class="truncate">{{ $a['project'] }}</span>
                      @endif
                    </div>

                    {{-- Acción rápida opcional al proyecto --}}
                    @if(!empty($a['projectId']))
                      <div class="mt-1">
                      <flux:navlist.item 
    :href="route('projects.show', $a['projectId'])" 
    wire:navigate
>
    {{ __('Open project') }} →
</flux:navlist.item>
                      </div>
                    @endif
                  </div>

                  {{-- Puntito de color (si lo envías en $a['color']) --}}
                  @php $color = $a['color'] ?? 'text-base-content/60'; @endphp
                  <span class="hidden sm:inline-flex h-1.5 w-1.5 rounded-full {{ $color }}"></span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
