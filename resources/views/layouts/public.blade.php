<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" data-theme="flux">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>{{ $title ?? 'Open Projects' }}</title>

  {{-- Vite --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Google Font: Inter --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen flex flex-col antialiased text-base-content 
             bg-gradient-to-b from-sky-50 via-white to-white
             font-sans"
      style="font-family: Inter, ui-sans-serif, system-ui">

  {{-- HEADER (alineado al contenedor) --}}
  <header class="sticky top-0 z-40 border-b border-base-300/70 bg-white/90 backdrop-blur">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
          <img src="../images/icon.png" alt="Logo" class="h-8 w-auto">
          <span class="hidden sm:inline text-sm font-semibold text-gray-900">Corworth</span>
        </a>
      </div>

      {{-- Navegación simple (oculta en móvil) --}}
     
      {{-- CTA derecha (opcional) --}}
      <div class="hidden lg:flex">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-900 hover:text-primary transition-colors">
          Log in <span aria-hidden="true">→</span>
        </a>
      </div>

      {{-- Botón móvil (solo icono) --}}
      <button type="button" class="lg:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100" aria-label="Open menu"
              x-data @click="$refs.mobileMenu.showModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6">
          <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    {{-- Menú móvil puro <dialog> (sin librerías) --}}
    <dialog x-ref="mobileMenu" class="rounded-xl p-0 w-full max-w-sm backdrop:bg-black/20">
      <div class="bg-white border border-base-300 rounded-xl overflow-hidden">
        <div class="px-4 py-3 flex items-center justify-between border-b border-base-300">
          <div class="flex items-center gap-2">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" class="h-7 w-auto" alt="">
            <span class="text-sm font-semibold">Menu</span>
          </div>
          <button class="p-2 rounded-md hover:bg-gray-100" @click="$refs.mobileMenu.close()" aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6">
              <path d="M6 18 18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <div class="p-2">
         
          <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-900 hover:bg-gray-50">Log in</a>
        </div>
      </div>
    </dialog>
  </header>

  {{-- BLOBS decorativos sutiles (compatibles Tailwind v3) --}}
  <div aria-hidden="true" class="pointer-events-none">
    <div class="absolute inset-x-0 -top-24 -z-10 overflow-hidden blur-3xl">
      <div class="relative left-1/2 -translate-x-1/2 h-72 w-[36rem] rotate-[30deg]
                  bg-gradient-to-tr from-pink-300 to-indigo-300 opacity-30
                  rounded-[40%/60%]">
      </div>
    </div>
  </div>

  {{-- CONTENIDO (slot) alineado al container --}}
  <main class="flex-1 w-full">
    <div class="max-w-6xl mx-auto px-6 py-8">
      {{ $slot }}
    </div>
  </main>

  {{-- FOOTER alineado --}}
  <footer class="border-t border-base-300/70">
    <div class="max-w-6xl mx-auto px-6 py-4 text-xs text-base-content/60 flex items-center justify-between">
      <span>© {{ date('Y') }} Corworth</span>
      <span>{{ now()->format('M d, Y H:i') }}</span>
    </div>
  </footer>

  {{-- Alpine (opcional) para el dialog; si ya lo tienes, omite esto --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
