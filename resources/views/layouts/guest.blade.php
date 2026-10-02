<!doctype html>
<html lang="pl">

<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, theme-value" />
  <title>Logowanie</title>
  <!-- CSS Tablera -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/css/tabler.min.css">
</head>

<body class="d-flex flex-column">
  
  <div class="page">
   
    <div class="page-wrapper">
      <main class="page-body">
        @yield('content')
        
      </main>
    </div>
  </div>
  <!-- JS Tablera -->
  <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.6.0/dist/js/tabler.min.js" defer></script>
</body>

</html>