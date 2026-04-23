<x-layouts.portal title="Form Pembayaran Zakat">

  @push('styles')
  <style>
    .type-btn.active {
      border-color: #10b981;
      background-color: #f0fdf4;
      color: #047857;
    }

    /* Menghilangkan panah atas/bawah pada input number */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
  </style>
  @endpush

  <div class="max-w-3xl mx-auto px-6 py-10">
    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-500 bg-white border border-amber-100 px-3 py-1 rounded-full mb-4 shadow-sm">
        🔒 Aman & Transparan di Blockchain
      </div>
      <h1 class="text-2xl font-extrabold text-slate-800">Tunaikan ZIS Anda dengan</h1>
      <h2 class="text-2xl font-extrabold text-emerald-500 mb-2">Aman dan Mudah</h2>
      <p class="text-xs text-slate-400 font-medium">Pembayaran tercatat permanen di Polygon Blockchain</p>
    </div>

    <div class="max-w-2xl mx-auto space-y-6">

      {{-- PILIH JENIS DANA --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <label class="block text-slate-700 font-semibold mb-3 text-sm">Pilih Jenis Dana</label>
          <div class="grid grid-cols-3 gap-3">
            <button type="button" class="type-btn active flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 transition-all hover:border-emerald-300" data-type="Zakat">
              <span class="text-xl mb-1">🌙</span><span class="text-[10px] font-semibold">Zakat</span>
            </button>
            <button type="button" class="type-btn flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 transition-all hover:border-emerald-300" data-type="Infak">
              <span class="text-xl mb-1">❤️</span><span class="text-[10px] font-semibold">Infak/Sedekah</span>
            </button>
            <button type="button" class="type-btn flex flex-col items-center justify-center p-3 rounded-xl border border-slate-200 transition-all hover:border-emerald-300" data-type="DSKL">
              <span class="text-xl mb-1">🤝</span><span class="text-[10px] font-semibold">DSKL</span>
            </button>
          </div>
        <div id="infoBox" class="mt-4 flex items-center gap-1.5 text-blue-500 text-[10px] font-medium bg-blue-50 w-max px-2 py-1 rounded">
          <span>ℹ️</span> <span id="infoText">Kewajiban 2.5% dari harta</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        {{-- SUB JENIS DANA --}}
        <div class="mb-5" id="containerSubJenis">
          <label id="labelSubJenis" class="block text-slate-700 font-semibold mb-2 text-sm">Sub Jenis Dana</label>
          <select id="subJenisDana" class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
          </select>
        </div>

        {{-- NOMINAL PEMBAYARAN --}}
        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-slate-700 font-semibold text-sm">Nominal Pembayaran</label>
            <div class="flex bg-slate-100 p-0.5 rounded-md border border-slate-200">
              <button type="button" id="btnEth" class="px-3 py-1 bg-white shadow-sm rounded text-[10px] font-bold text-slate-700 transition-all">ETH</button>
              <button type="button" id="btnIdr" class="px-3 py-1 rounded text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-all">IDR</button>
            </div>
          </div>
          <div class="flex">
            <span id="labelCurrency" class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm font-semibold">
              ETH
            </span>
            <input type="number" id="nominal" step="0.01" placeholder="0.00" class="flex-1 w-full bg-white border border-slate-300 rounded-none rounded-r-lg px-4 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
          </div>
          {{-- KALKULASI REAL TIME AKAN MUNCUL DI SINI --}}
          <p id="priceHelper" class="text-[11px] text-emerald-600 mt-2 font-bold bg-emerald-50 px-2 py-1 inline-block rounded">
            Memuat harga ETH...
          </p>
        </div>
      </div>

      {{-- DATA PEMBAYAR --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-slate-700 font-bold text-sm">Data Pembayar</h3>
          {{-- CHECKBOX ANONIM (HAMBA ALLAH) --}}
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="is_anonim" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
            <span class="text-xs font-bold text-slate-600">Sembunyikan Nama (Hamba Allah)</span>
          </label>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-slate-600 font-medium mb-1 text-xs">Nama Lengkap</label>
            <input type="text" id="nama" placeholder="Masukkan nama lengkap" value="{{ Auth::check() && Auth::user()->name != 'Hamba Allah' ? Auth::user()->name : '' }}" class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500 transition-all disabled:bg-slate-100 disabled:text-slate-400">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-600 font-medium mb-1 text-xs">Nomor Handphone</label>
              <input type="text" placeholder="08xx-xxxx-xxxx" class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-slate-600 font-medium mb-1 text-xs">Email</label>
              {{-- PERBAIKAN LOGIKA EMAIL: Mencegah email dummy @zakat.local tampil di form --}}
              <input type="email" id="email" placeholder="email@contoh.com" value="{{ Auth::check() && !str_contains(Auth::user()->email, '@zakat.local') ? Auth::user()->email : '' }}" class="w-full bg-white border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500">
            </div>
          </div>
        </div>
      </div>

      {{-- BREAKDOWN BOX --}}
      <div id="breakdownBox" class="bg-amber-50 border border-amber-200 rounded-xl p-4 hidden">
        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-3">
          💡 Rincian Pembayaran Anda
        </p>
        <div class="space-y-2 text-xs">
          <div class="flex justify-between text-slate-600">
            <span class="font-medium">Total dibayarkan</span>
            <span id="bd-total" class="font-black font-mono text-slate-800"></span>
          </div>
          <div class="flex justify-between text-slate-600">
            <span class="font-medium">Dana ZIS (87.5%)</span>
            <span id="bd-zis" class="font-black font-mono text-emerald-700"></span>
          </div>
          <div class="flex justify-between text-slate-500">
            <span class="font-medium flex items-center gap-1">
              Hak Amil BAZNAS (12.5%)
              <span class="bg-amber-200 text-amber-800 text-[9px] px-1.5 py-0.5 rounded font-black">Operasional</span>
            </span>
            <span id="bd-amil" class="font-bold font-mono text-amber-600"></span>
          </div>
          <div class="border-t border-amber-200 pt-2 mt-1">
            <p class="text-[10px] text-amber-600 font-semibold leading-relaxed">
              ⚠️ Dana yang tersalurkan ke mustahik adalah <strong>87.5%</strong> dari nominal yang Anda bayarkan.
              Sisanya 12.5% merupakan hak amil sesuai syariat Islam (QS. At-Taubah: 60).
            </p>
          </div>
        </div>
      </div>

      <button type="button" id="btnProceed" class="w-full bg-[#059669] hover:bg-[#047857] text-white font-bold py-3.5 rounded-lg shadow-md transition-all text-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
        </svg>
        <span id="btnText">{{ Auth::check() ? 'LANJUTKAN PEMBAYARAN' : 'CONNECT & BAYAR' }}</span>
      </button>
    </div>
  </div>

  @push('scripts')
  <script>
    const CONTRACT_ADDRESS = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
    const ABI_VAULT = ["function bayarZIS(string memory _tipeDana) public payable"];

    let isUserAuth = @json(Auth::check());
    let zakatType = "Zakat";
    let currentCurrency = "ETH";
    let ethPriceInIdr = 50000000;

    // DATA MAPPING SUB-JENIS
    const subJenisDataMap = {
      'Zakat': { label: 'Sub Jenis Zakat', info: 'Kewajiban 2.5% dari harta', options: ['Zakat Maal', 'Zakat Penghasilan', 'Zakat Fitrah'] },
      'Infak': { label: '', info: 'Sedekah membersihkan jiwa', options: [] },
      'DSKL':  { label: '', info: 'Dana Sosial Keagamaan Lainnya', options: [] }
    };

    const containerSubJenis = document.getElementById('containerSubJenis');
    const selectSubJenis = document.getElementById('subJenisDana');
    const labelSubJenis = document.getElementById('labelSubJenis');
    const infoText = document.getElementById('infoText');
    const inputNominal = document.getElementById('nominal');
    const priceHelper = document.getElementById('priceHelper');

    function updateFormDisplay(type) {
      const data = subJenisDataMap[type];
      infoText.innerText = data.info;
      selectSubJenis.innerHTML = '';
      if (data.options.length > 0) {
        containerSubJenis.classList.remove('hidden');
        labelSubJenis.innerText = data.label;
        data.options.forEach(opt => {
          let el = document.createElement('option');
          el.value = opt; el.innerText = opt;
          selectSubJenis.appendChild(el);
        });
      } else {
        containerSubJenis.classList.add('hidden');
      }
    }

    updateFormDisplay('Zakat');

    // Fetch Harga ETH
    fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=idr')
      .then(res => res.json())
      .then(data => {
        ethPriceInIdr = data.ethereum.idr;
        calculateRealTime();
      })
      .catch(err => console.log("Gagal memuat harga ETH terbaru."));

    // LOGIKA CHECKBOX ANONIM (HAMBA ALLAH)
    const chkAnonim = document.getElementById('is_anonim');
    const inputNama = document.getElementById('nama');
    let namaAsli = inputNama.value;

    chkAnonim.addEventListener('change', (e) => {
      if(e.target.checked) {
        namaAsli = inputNama.value;
        inputNama.value = 'Hamba Allah';
        inputNama.disabled = true;
      } else {
        inputNama.value = namaAsli === 'Hamba Allah' ? '' : namaAsli;
        inputNama.disabled = false;
      }
    });

    // LOGIKA KALKULASI REAL-TIME TERBARU (DENGAN BREAKDOWN)
    function calculateRealTime() {
      const val = parseFloat(inputNominal.value);

      if (!val || val <= 0) {
        priceHelper.innerText = '';
        // Sembunyikan breakdown jika nominal kosong
        document.getElementById('breakdownBox').classList.add('hidden');
        return;
      }

      // Hitung breakdown amil
      const hakAmil       = val * 0.125;
      const nominalBersih = val - hakAmil;

      if (currentCurrency === 'ETH') {
        priceHelper.innerText = `Setara: Rp ${(val * ethPriceInIdr).toLocaleString('id-ID')} (via CoinGecko)`;
        document.getElementById('bd-total').innerText = val.toFixed(6) + ' ETH';
        document.getElementById('bd-zis').innerText   = nominalBersih.toFixed(6) + ' ETH';
        document.getElementById('bd-amil').innerText  = hakAmil.toFixed(6) + ' ETH';
      } else {
        priceHelper.innerText = `Setara: ${(val / ethPriceInIdr).toFixed(6)} ETH (via CoinGecko)`;
        document.getElementById('bd-total').innerText = 'Rp ' + val.toLocaleString('id-ID');
        document.getElementById('bd-zis').innerText   = 'Rp ' + (val * 0.875).toLocaleString('id-ID');
        document.getElementById('bd-amil').innerText  = 'Rp ' + (val * 0.125).toLocaleString('id-ID');
      }

      // Tampilkan breakdown
      document.getElementById('breakdownBox').classList.remove('hidden');
    }

    inputNominal.addEventListener('input', calculateRealTime);

    document.querySelectorAll('.type-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        zakatType = btn.dataset.type;
        updateFormDisplay(zakatType);
      });
    });

    const btnEth = document.getElementById('btnEth');
    const btnIdr = document.getElementById('btnIdr');
    const labelCurrency = document.getElementById('labelCurrency');

    btnIdr.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentCurrency === 'IDR') return;
      currentCurrency = 'IDR';
      btnIdr.classList.add('bg-white', 'shadow-sm', 'text-slate-700');
      btnEth.classList.remove('bg-white', 'shadow-sm', 'text-slate-700');
      labelCurrency.innerText = 'Rp';
      if (inputNominal.value) inputNominal.value = Math.round(parseFloat(inputNominal.value) * ethPriceInIdr);
      calculateRealTime();
    });

    btnEth.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentCurrency === 'ETH') return;
      currentCurrency = 'ETH';
      btnEth.classList.add('bg-white', 'shadow-sm', 'text-slate-700');
      btnIdr.classList.remove('bg-white', 'shadow-sm', 'text-slate-700');
      labelCurrency.innerText = 'ETH';
      if (inputNominal.value) inputNominal.value = (parseFloat(inputNominal.value) / ethPriceInIdr).toFixed(4);
      calculateRealTime();
    });

    // Eksekusi Pembayaran
    document.getElementById('btnProceed').addEventListener('click', async (e) => {
      e.preventDefault();
      if (!window.ethereum) return alert("MetaMask tidak ditemukan!");

      const rawNominal = inputNominal.value;
      if (!rawNominal || rawNominal <= 0) return alert("Silakan isi nominal pembayaran!");

      let finalEthValue = rawNominal;
      if (currentCurrency === 'IDR') {
        finalEthValue = (parseFloat(rawNominal) / ethPriceInIdr).toString();
      }

      // ✅ LOGIKA BARU: Silent Login - Menghubungkan sesi tanpa Redirect halaman!
      if (!isUserAuth) {
        try {
            if (typeof showGlobalLoader === 'function') showGlobalLoader("🦊", "Autentikasi Aman", "Menyiapkan sesi pembayaran Anda...");

            const provider = new ethers.BrowserProvider(window.ethereum);
            const signer = await provider.getSigner();
            const wallet = await signer.getAddress();

            // Lakukan login ke backend secara diam-diam
            const loginRes = await fetch("{{ route('login.wallet') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    wallet_address: wallet,
                    role: 'muzakki' // Default otomatis untuk pembayar
                })
            });

            const loginData = await loginRes.json();
            if (loginData.status !== 'success') {
                throw new Error(loginData.message || "Gagal membuat sesi login.");
            }
            
            // Sesi sukses dibuat! Kita lanjutkan tanpa me-refresh halaman.
            isUserAuth = true; 
        } catch (error) {
            console.error(error);
            if (document.getElementById('loader')) document.getElementById('loader').classList.add('hidden');
            return alert("Gagal melakukan autentikasi: " + error.message);
        }
      }

      try {
        if (typeof showGlobalLoader === 'function') showGlobalLoader("🛡️", "Konfirmasi", "Setujui transaksi di MetaMask.");

        const provider = new ethers.BrowserProvider(window.ethereum);
        const signer = await provider.getSigner();
        const contract = new ethers.Contract(CONTRACT_ADDRESS, ABI_VAULT, signer);

        const selectedSub = selectSubJenis.value || "";
        const fullType = selectedSub ? `${zakatType} - ${selectedSub}` : zakatType;

        const tx = await contract.bayarZIS(fullType, {
          value: ethers.parseEther(finalEthValue.toString())
        });

        if (typeof showGlobalLoader === 'function') showGlobalLoader("🔄", "Memproses", "Menunggu konfirmasi blockchain...");
        await tx.wait();

        if (typeof showGlobalLoader === 'function') showGlobalLoader("💾", "Menyimpan", "Mencatat transaksi ke sistem...");

        // Kirim data ke Controller (dengan || null agar validation nullable laravel lolos)
        const response = await fetch("{{ route('muzakki.transaction.store') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            jenis_dana: fullType,
            nominal: finalEthValue,
            tx_hash: tx.hash,
            nama: inputNama.value || null,
            email: document.getElementById('email').value || null,
            is_anonim: chkAnonim.checked
          })
        });

        const data = await response.json();

        // Pengecekan Error API (Mencegah Silent Failure)
        if (!response.ok || data.status !== 'success') {
           throw new Error(data.message || "Gagal menyimpan data ke database server.");
        }

        alert("Alhamdulillah, Pembayaran berhasil dan tercatat!");
        
        // Redirect difokuskan ke Dashboard yang biasanya menampung History
        window.location.href = "{{ route('muzakki.dashboard') }}";

      } catch (error) {
        console.error(error);
        if (document.getElementById('loader')) document.getElementById('loader').classList.add('hidden');
        alert("Terjadi kesalahan: " + error.message);
      }
    });
  </script>
  @endpush
</x-layouts.portal>