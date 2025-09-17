{{-- resources/views/livewire/dashboard/main.blade.php --}}
<div class="p-8 space-y-8 w-full" x-data>
  {{-- ===== Header ===== --}}
  <div>
    <div class="text-xl font-semibold">Projects</div>
    <div class="text-sm text-base-content/60">Overview and pending projects</div>
  </div>

  {{-- ===== KPI CARDS (animadas + sparkline opcional) ===== --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($cards as $c)
      @php
        $title   = (string)($c['title'] ?? '');
        $value   = (string)($c['value'] ?? '—');
        $valueNr = (int) str_replace([',',' '],'', $c['value'] ?? 0);
        $delta   = (string)($c['delta'] ?? '');
        $iconKey = (string)($c['icon'] ?? '');
        $spark   = isset($c['spark']) && is_array($c['spark']) ? $c['spark'] : [];

        if ($iconKey === '') {
          $t = strtolower($title);
          if     (str_contains($t,'pending'))              $iconKey = 'clock';
          elseif (str_contains($t,'progress'))             $iconKey = 'bolt';
          elseif (str_contains($t,'approved'))             $iconKey = 'check';
          elseif (str_contains($t,'cancel'))               $iconKey = 'alert';
          elseif (str_contains($t,'project'))              $iconKey = 'folder';
          elseif (str_contains($t,'user') || str_contains($t,'member')) $iconKey = 'users';
          elseif (str_contains($t,'revenue') || str_contains($t,'sales')) $iconKey = 'trending-up';
          else $iconKey = 'chart';
        }

        if     ($iconKey === 'clock') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/></svg>'; $iconTint = 'bg-amber-100 text-amber-700 border-amber-200'; }
        elseif ($iconKey === 'bolt')  { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>'; $iconTint = 'bg-indigo-100 text-indigo-700 border-indigo-200'; }
        elseif ($iconKey === 'check') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'; $iconTint = 'bg-emerald-100 text-emerald-700 border-emerald-200'; }
        elseif ($iconKey === 'alert') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>'; $iconTint = 'bg-rose-100 text-rose-700 border-rose-200'; }
        elseif ($iconKey === 'users') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'; $iconTint = 'bg-sky-100 text-sky-700 border-sky-200'; }
        elseif ($iconKey === 'trending-up') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 7-7"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 4h7v7"/></svg>'; $iconTint = 'bg-teal-100 text-teal-700 border-teal-200'; }
        elseif ($iconKey === 'folder') { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M3 7h6l2 2h10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>'; $iconTint = 'bg-base-100 text-base-content/70 border-base-300'; }
        else { $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="M7 13h3v5H7zM12 8h3v10h-3zM17 11h3v7h-3z"/></svg>'; $iconTint = 'bg-base-100 text-base-content/70 border-base-300'; }

        $isNeg = str_starts_with($delta, '-');
        if ($isNeg) {
          $deltaIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 15l-6 6-6-6"/></svg>';
          $deltaClass = 'text-rose-700 bg-rose-50 border-rose-200';
        } else {
          $deltaIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6-6 6 6"/></svg>';
          $deltaClass = 'text-emerald-700 bg-emerald-50 border-emerald-200';
        }

        // Sparkline simple
        $sparkW = 160; $sparkH = 46; $pad = 6;
        $sparkPath = '';
        if (!empty($spark)) {
          $minV = min($spark); $maxV = max($spark);
          if ($maxV === $minV) { $maxV = $minV + 1; }
          $n = count($spark);
          $dx = $n > 1 ? ($sparkW - 2*$pad) / ($n - 1) : 0;
          $d = [];
          foreach ($spark as $i => $v) {
              $x = $pad + $dx * $i;
              $y = $pad + ($sparkH - 2*$pad) * (1 - (($v - $minV) / max(1e-9, ($maxV - $minV))));
              $d[] = ($i === 0 ? 'M' : 'L') . round($x,1) . ' ' . round($y,1);
          }
          $sparkPath = implode(' ', $d);
        }
      @endphp

      <div
        class="card border border-base-300 bg-gradient-to-br from-base-200 to-base-100"
        x-data="{
          val: 0,
          target: {{ $valueNr }},
          animate() {
            const d = this.target;
            const dur = 700;
            const start = performance.now();
            const step = (t) => {
              const p = Math.min((t - start) / dur, 1);
              const e = 1 - Math.pow(1 - p, 3);
              this.val = Math.round(d * e);
              if (p < 1) requestAnimationFrame(step);
            };
            requestAnimationFrame(step);
          }
        }"
        x-init="animate()"
      >
        <div class="card-body p-5">
          <div class="flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl border flex items-center justify-center {{ $iconTint }}">
              {!! $iconSvg !!}
            </div>

            <div class="min-w-0 flex-1">
              <div class="text-sm text-base-content/70 truncate">{{ $title }}</div>
              <div class="mt-1 text-3xl font-bold tracking-tight tabular-nums">
                <span x-text="val.toLocaleString()">{{ $value }}</span>
              </div>

              @if($delta !== '')
                <div
                  class="mt-2 inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full border {{ $deltaClass }} opacity-0 translate-y-1"
                  x-intersect="$el.classList.remove('opacity-0','translate-y-1')"
                  style="transition: all .4s ease;"
                >
                  {!! $deltaIcon !!}
                  <span class="font-medium">{{ $delta }}</span>
                </div>
              @endif
            </div>
          </div>

          
        </div>
      </div>
    @endforeach
  </div>

  {{-- ===== Second row: Bar Chart + Weekly Trend + Tasks + Recent Activity ===== --}}
  <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

    {{-- ==== Bar Chart: Project Status ==== --}}
    @php
      $getVal = function($label) use ($cards){
        foreach ($cards as $c) {
          if (strtolower($c['title']??'')===strtolower($label)) {
            return (int) str_replace([',',' '],'', $c['value'] ?? 0);
          }
        }
        return 0;
      };

      $bars = [
        ['label'=>'Approved',    'value'=>$getVal('Approved'),    'color'=>'#10b981'],
        ['label'=>'In Progress', 'value'=>$getVal('In Progress'), 'color'=>'#6366f1'],
        ['label'=>'Pending',     'value'=>$getVal('Pending'),     'color'=>'#f59e0b'],
      ];
      $maxVal = max(array_column($bars,'value')) ?: 1;
    @endphp

    <div class="card bg-base-200 border border-base-300 lg:col-span-1">
      <div class="card-body p-6">
        <div class="font-semibold mb-4">Project Status</div>
        <div class="space-y-4">
          @foreach($bars as $b)
            @php $pct = ($b['value'] / $maxVal) * 100; @endphp
            <div>
              <div class="flex justify-between text-sm mb-1">
                <span>{{ $b['label'] }}</span>
                <span class="text-base-content/70">{{ $b['value'] }}</span>
              </div>
              <div class="w-full h-3 rounded bg-base-300 overflow-hidden">
                <div class="h-3 rounded"
     x-init="$el.style.width='0%'; setTimeout(()=>{$el.style.width='{{ $pct }}%'}, 80)"
     @style(["transition: width .8s ease; background: $b[color]"])>
</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ==== Weekly Trend (línea doble) ==== --}}
    @php
      $L = $chart['labels']   ?? [];
      $A = $chart['approved'] ?? [];
      $C = $chart['created']  ?? [];

      $w = 320; $h = 160; $pad = 24;
      $plotW = $w - $pad*2; $plotH = $h - $pad*2;

      $maxY = max(1, max($A ?: [0]), max($C ?: [0]));
      $n = max(count($L), 1);
      $dx = $n > 1 ? $plotW / ($n - 1) : 0;

      $mkPath = function(array $series) use ($pad,$plotH,$dx,$maxY) {
          if (empty($series)) return '';
          $d = [];
          foreach ($series as $i => $v) {
              $x = $pad + $dx * $i;
              $y = $pad + $plotH * (1 - ($v / max(1e-9, $maxY)));
              $d[] = ($i === 0 ? 'M' : 'L') . round($x,1) . ' ' . round($y,1);
          }
          return implode(' ', $d);
      };
      $pathApproved = $mkPath($A);
      $pathCreated  = $mkPath($C);
    @endphp

    <div class="card bg-base-200 border border-base-300 lg:col-span-1">
      <div class="card-body p-6">
        <div class="font-semibold mb-1">Weekly Trend</div>
        <div class="text-xs text-base-content/60 mb-4">Created vs Approved (last 7 days)</div>

        <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full">
          @for($i=0;$i<=4;$i++)
            @php $gy = $pad + ($plotH/4)*$i; @endphp
            <line x1="{{ $pad }}" y1="{{ $gy }}" x2="{{ $w - $pad }}" y2="{{ $gy }}" class="stroke-base-300" stroke-width="1"/>
          @endfor

          <line x1="{{ $pad }}" y1="{{ $pad }}" x2="{{ $pad }}" y2="{{ $h - $pad }}" class="stroke-base-300" stroke-width="1"/>
          <line x1="{{ $pad }}" y1="{{ $h - $pad }}" x2="{{ $w - $pad }}" y2="{{ $h - $pad }}" class="stroke-base-300" stroke-width="1"/>

          <path d="{{ $pathApproved }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round">
            <animate attributeName="stroke-dasharray" from="0,1000" to="1000,0" dur="0.8s" fill="freeze"/>
          </path>
          <path d="{{ $pathCreated }}" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="6 6">
            <animate attributeName="stroke-dasharray" from="0,1000" to="6 6" dur="0.8s" fill="freeze"/>
          </path>

          @foreach($L as $i => $lab)
            @php $x = $pad + $dx * $i; @endphp
            <text x="{{ $x }}" y="{{ $h - 6 }}" text-anchor="middle" class="fill-base-content/60 text-[10px]">{{ $lab }}</text>
          @endforeach
        </svg>

        <div class="mt-3 flex items-center gap-4 text-xs">
          <span class="inline-flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full" style="background:#10b981"></span> Approved
          </span>
          <span class="inline-flex items-center gap-2">
            <span class="inline-block w-3 h-3 rounded-full" style="background:#6366f1"></span> Created
          </span>
        </div>
      </div>
    </div>

    {{-- ==== Tasks (pending projects) ==== --}}
    <div class="card bg-base-200 border border-base-300 lg:col-span-2">
      <div class="card-body p-0">
        <div class="px-6 py-4 border-b border-base-300 flex items-center justify-between">
          <div class="font-semibold">Tasks</div>
          @if (Route::has('projects.index'))
            <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-xs">View all</a>
          @endif
        </div>

        <div class="overflow-x-auto">
          <table class="table w-full">
            <thead class="bg-base-200/70">
              <tr class="text-xs text-base-content/60">
                <th class="px-6">Name</th>
                <th class="w-40">Seller</th>
                <th class="w-32">Updated</th>
                <th class="w-40">Status</th>
              </tr>
            </thead>
          <tbody class="[&>tr]:border-t [&>tr]:border-base-300 [&>tr]:even:bg-base-100/50">
  @forelse($pendingProjects as $p)
    <tr class="align-middle hover:bg-base-100/70">
      {{-- Name + Building --}}
      <td class="px-6">
        @if (Route::has('projects.show'))
          <a href="{{ route('projects.show', $p['id']) }}" class="link link-hover font-medium">
            {{ $p['name'] ?? '—' }}
          </a>
        @else
          <span class="font-medium">{{ $p['name'] ?? '—' }}</span>
        @endif
        <div class="text-xs text-base-content/60">{{ $p['building'] ?? '—' }}</div>
      </td>

      {{-- Seller (texto simple) --}}
      <td class="text-sm text-base-content/70 whitespace-nowrap">
        @php
          $sellerName = $p['seller'] ?? $p['seller_name'] ?? null;
          $sellerId   = $p['seller_id'] ?? null;
        @endphp

        @if($sellerName)
          @if (Route::has('sellers.show') && $sellerId)
            <a href="{{ route('sellers.show', $sellerId) }}" class="link link-hover">
              {{ $sellerName }}
            </a>
          @else
            {{ $sellerName }}
          @endif
        @else
          <span class="text-xs text-base-content/60">—</span>
        @endif
      </td>

      {{-- Updated --}}
      <td class="text-sm text-base-content/70 whitespace-nowrap">
        {{ $p['updated'] ?? '—' }}
      </td>

      {{-- Status --}}
      <td class="whitespace-nowrap pr-6">
        <x-status-pill
          :key="Str::of($p['gen_key'] ?? 'default')->lower()"
          :label="($p['gen_label'] ?? '—')"
          size="xs"
          variant="outline"
          :dot="true"
        />
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="4" class="py-10 text-center text-base-content/60">No tasks to show.</td>
    </tr>
  @endforelse
</tbody>

          </table>
        </div>
      </div>
    </div>

    {{-- ==== Recent Activity ==== --}}
    <div class="card bg-base-200 border border-base-300 lg:col-span-1">
      <div class="card-body p-6 space-y-6">
        <div class="flex items-start justify-between">
          <div>
            <div class="font-semibold">Recent Activity</div>
            <div class="text-xs text-base-content/60">Latest project updates • {{ now()->format('M d, Y') }}</div>
          </div>
          @if (Route::has('activity.index'))
            <a href="{{ route('activity.index') }}" class="btn btn-ghost btn-xs">View all</a>
          @endif
        </div>

        <div class="relative">
          <div class="absolute left-[11px] top-2 bottom-2 w-px bg-base-300/70"></div>
          <div class="space-y-4">
            @forelse(($activity ?? []) as $a)
              <div class="relative pl-8">
                <div class="absolute left-0 top-[2px]">
                  <div class="w-5 h-5 rounded-full bg-base-100 border border-base-300 flex items-center justify-center">
                    <span class="{{ $a['color'] ?? 'text-base-content/60' }} text-[11px] leading-none">
                      {{ $a['icon'] ?? '•' }}
                    </span>
                  </div>
                </div>

                <div class="min-w-0">
                  <div class="text-sm font-medium truncate">
                    @if (Route::has('projects.show') && !empty($a['projectId']))
                      <a href="{{ route('projects.show', $a['projectId']) }}" class="link link-hover">
                        {{ $a['title'] ?? 'Activity' }} — {{ $a['project'] ?? 'Project' }}
                      </a>
                    @else
                      {{ $a['title'] ?? 'Activity' }} — {{ $a['project'] ?? 'Project' }}
                    @endif
                  </div>
                  @if(!empty($a['body']))
                    <div class="text-xs text-base-content/70 line-clamp-2">{{ $a['body'] }}</div>
                  @endif
                  <div class="mt-1 text-[11px] text-base-content/50">
                    {{ $a['user'] ?? 'System' }} • {{ $a['when'] ?? '' }}
                  </div>
                </div>
              </div>
            @empty
              <div class="text-sm text-base-content/60">No recent activity.</div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
