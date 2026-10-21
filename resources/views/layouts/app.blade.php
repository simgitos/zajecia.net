<!doctype html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Zajęciownia.pl') }}</title>

  <!-- CSS Tablera (Bootstrap 5) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/css/tabler.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.48.0/dist/tabler-icons.min.css" />
  @livewireStyles

</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/js/tabler-theme.min.js"
    integrity="sha384-VYrlUJTOZnyvJK9yOqGRxAsoXJXBwihg6h2A8GQqzBsNgyN6yc28AZqjqcOBEw4h"
    crossorigin="anonymous"></script>
  <div class="page">
    @include('layouts.partials.sidebar')

    <!-- Górny pasek i główna treść -->
    <div class="page-wrapper">
    <div class="d-none d-lg-block">
    @include('layouts.partials.topbar')
    </div> 

      <!-- Główna zawartość strony -->
      <main class="page-body">
        <div class="container-xl">
          @include('partials.terms-alert')
          {{ $slot ?? '' }}
          @yield('content')
        </div>
      </main>
      @include('layouts.partials.copy')
    </div>
  </div>

  <!-- Skrypty JS Tablera i Livewire -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/js/tabler.min.js" defer></script>
  @livewireScripts
</body>

</html>