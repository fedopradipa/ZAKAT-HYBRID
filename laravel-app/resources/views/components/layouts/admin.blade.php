{{-- resources/views/layouts/admin.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Admin Panel' }} - BAZNAS Web3</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
  </style>
  @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800 antialiased flex h-screen overflow-hidden">

  <x-partials.admin-sidebar />

  <div class="flex-1 flex flex-col w-full h-full">
    <x-partials.admin-header title="{{ $title ?? 'Dashboard' }}" />

    <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
      {{ $slot }}
    </main>
  </div>

  {{-- ✅ TAMBAHKAN INI: Ethers.js v6 wajib load sebelum @stack('scripts') --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>

  @stack('scripts')
</body>

</html>