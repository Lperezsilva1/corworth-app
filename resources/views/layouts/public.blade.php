<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover" />
  <title>{{ $title ?? 'Open Projects' }}</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&display=swap" rel="stylesheet">

  <style>
    html, body { margin:0; padding:0; height:100%; width:100%; background:#000; }
    body { font-family: Inter, ui-sans-serif, system-ui; color:#fff; }

 :root{
  --tv-base: 19px;
  --safe-x: 60px;   /* antes 36px → más margen lateral */
  --safe-y: 36px;   /* antes 24px → más aire arriba/abajo */
  --radius: 18px;
}
    @media (min-width:1536px){ :root{ --tv-base: 20px; } }

    .tv {
      min-height: 100vh;
      font-size: var(--tv-base);
      line-height: 1.35;
      position: relative;
      padding: var(--safe-y) var(--safe-x);
      /* sutil noise + gradient base */
       background:
    radial-gradient(120% 140% at 50% -20%, rgba(255,255,255,0.10), transparent 60%), /* luz blanca más intensa */
    radial-gradient(120% 140% at 50% -20%, rgba(96,165,250,0.15), transparent 60%), /* azul */
    radial-gradient(130% 150% at 80% 110%, rgba(34,211,238,0.12), transparent 60%), /* cyan */
    linear-gradient(180deg, #0b0b0c 0%, #0e0e10 100%);
      isolation: isolate;
    }
    /* Vignette */
    .tv::before{
      content:""; position:fixed; inset:0; pointer-events:none; z-index:0;
       background: radial-gradient(120% 120% at 50% 50%, rgba(255,255,255,0.05) 0%, rgba(0,0,0,0) 60%, rgba(0,0,0,.45) 100%);
    }
    /* Noise */
    .tv::after{
      content:""; position:fixed; inset:0; pointer-events:none; z-index:0; opacity:.07;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.15'/%3E%3C/svg%3E");
      mix-blend-mode: soft-light;
    }

    .tv-main { position:relative; z-index:1; width:100%; height:calc(100vh - (var(--safe-y)*2)); }

    /* Header TV con glow */
    .tv-title {
      font-size: clamp(26px, 3.2vw, 44px);
      font-weight: 800; letter-spacing:-0.01em;
      text-shadow: 0 8px 30px rgba(96,165,250,.18), 0 2px 8px rgba(0,0,0,.6);
    }
    .tv-sub { font-size: clamp(12px, 1.2vw, 15px); color:#cbd5e1cc; }
  </style>
</head>
<body>
  <div class="tv">
    <main class="tv-main">
      {{ $slot }}
    </main>
  </div>
</body>

</html>
