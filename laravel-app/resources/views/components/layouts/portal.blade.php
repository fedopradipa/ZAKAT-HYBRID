<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Portal BAZNAS Web3' }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.11.1/ethers.umd.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
    }
  </style>
  @stack('styles')
</head>

<body class="text-slate-800 flex flex-col min-h-screen">

  <x-partials.portal-navbar />

  <main class="flex-1">
    {{ $slot }}
  </main>

  <div id="loader" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] flex items-center justify-center hidden">
    <div class="bg-white p-8 rounded-2xl text-center max-w-sm w-full shadow-xl">
      <div class="text-4xl mb-4 animate-spin">⏳</div>
      <h3 class="text-lg font-bold text-slate-800" id="loaderTitle">Memproses...</h3>
      <p class="text-slate-500 text-xs mt-1" id="loaderDesc">Harap tunggu sebentar.</p>
    </div>
  </div>

  <script>
    // Sesuaikan Contract Address dengan hasil terminal deploy terbaru
    const CONTRACT_ADDRESS_GLOBAL = "0x5FbDB2315678afecb367f032d93F642f64180aa3";

    // ✅ TAMBAHAN: Ubah dari timPenyaluran menjadi akunPenyaluran
    const ABI_CHECK_GLOBAL = [
      "function adminKeuangan() view returns (address)",
      "function akunPemerintah() view returns (address)",
      "function akunPenyaluran() view returns (address)"
    ];

    // Status Auth dari Laravel (Anti-Formatter)
    let isUserAuthGlobal = @json(Auth::check());

    /**
     * Menampilkan Loading secara Global
     */
    function showGlobalLoader(icon, title, desc) {
      const loader = document.getElementById('loader');
      if (!loader) return;
      loader.classList.remove('hidden');
      document.getElementById('loaderTitle').innerText = title;
      document.getElementById('loaderDesc').innerText = desc;
    }

    /**
     * Logika Utama: Hubungkan Dompet & Otentikasi
     */
    async function connectAndAuth() {
      if (!window.ethereum) return alert("MetaMask tidak ditemukan!");

      showGlobalLoader("🦊", "Hubungkan Dompet", "Silakan konfirmasi di MetaMask...");

      try {
        const provider = new ethers.BrowserProvider(window.ethereum);
        const signer = await provider.getSigner();
        const wallet = await signer.getAddress();

        const contract = new ethers.Contract(CONTRACT_ADDRESS_GLOBAL, ABI_CHECK_GLOBAL, provider);

        // SESUDAH
        const [fin, gov, pen] = await Promise.all([
          contract.adminKeuangan(),
          contract.akunPemerintah(),
          contract.akunPenyaluran()
        ]);

        // ✅ DEBUG SEMENTARA
        console.log("=== DEBUG ROLE DETECTION ===");
        console.log("adminKeuangan  :", fin);
        console.log("akunPemerintah :", gov);
        console.log("akunPenyaluran :", pen);
        console.log("Wallet login   :", wallet);

        let role = "muzakki"; // Default
        const connectedWallet = wallet.toLowerCase();

        // ✅ TAMBAHAN: Deteksi Role Penyaluran menggunakan akunPenyaluran
        if (connectedWallet === fin.toLowerCase()) {
          role = "keuangan";
        } else if (connectedWallet === gov.toLowerCase()) {
          role = "pemerintah";
        } else if (connectedWallet === pen.toLowerCase()) {
          role = "penyaluran";
        }
        // TAMBAH INI TEPAT SETELAH CLOSING BRACE
        console.log("Role detected  :", role);
        console.log("============================");
        // PROSES KIRIM DATA
        const res = await fetch("{{ route('login.wallet') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            wallet_address: wallet,
            role: role,
            previous_url: window.location.href // MENGIRIM URL HALAMAN SAAT INI
          })
        });

        const data = await res.json();

        if (data.status === 'success') {
          // Redirect ke URL yang diperintahkan server
          window.location.href = data.redirect_url;
          return true;
        } else {
          throw new Error(data.message || "Gagal melakukan sinkronisasi wallet.");
        }

      } catch (e) {
        console.error("Auth Error:", e);
        document.getElementById('loader').classList.add('hidden');
        if (e.code === "ACTION_REJECTED") {
          alert("Koneksi dibatalkan di MetaMask.");
        } else {
          alert("Gagal Terhubung: " + e.message);
        }
        return false;
      }
    }

    /**
     * Event Listener Otomatis untuk Tombol Login di Navbar
     */
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('btnConnect');
      if (btn) {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          connectAndAuth();
        });
      }
    });
  </script>

  @stack('scripts')
</body>

</html>