{{-- resources/views/dashboard/penyaluran/index.blade.php --}}

<x-layouts.admin title="Dashboard & Buat Program">
  {{-- STATS CARDS --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border-2 border-slate-200 shadow-sm text-center md:text-left">
      <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Dana Terkumpul</p>
      <h3 class="text-2xl font-black text-slate-900">
        {{ number_format($totalTerkumpul ?? 0, 4) }}
        <span class="text-xs text-emerald-500 font-bold">ETH</span>
      </h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border-2 border-slate-200 shadow-sm text-center md:text-left">
      <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Disalurkan</p>
      <h3 class="text-2xl font-black text-rose-500">
        {{ number_format($totalDisalurkan ?? 0, 4) }}
        <span class="text-xs text-rose-400 font-bold">ETH</span>
      </h3>
    </div>
    <div class="bg-emerald-900 p-6 rounded-2xl shadow-xl text-white border-2 border-emerald-800 text-center md:text-left">
      <p class="text-emerald-300/70 text-[10px] font-black uppercase tracking-widest mb-1">Sisa Saldo Brankas</p>
      <h3 class="text-2xl font-black text-white">
        {{ number_format($sisaSaldo ?? 0, 4) }}
        <span class="text-xs text-emerald-400 font-bold">ETH</span>
      </h3>
    </div>
  </div>

  @if(session('success'))
  <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-xl shadow-sm">
    <p class="font-black text-sm">Berhasil!</p>
    <p class="text-xs font-semibold">{{ session('success') }}</p>
  </div>
  @endif

  @if(session('error'))
  <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-800 p-4 mb-6 rounded-r-xl shadow-sm">
    <p class="font-black text-sm">Gagal!</p>
    <p class="text-xs font-semibold">{{ session('error') }}</p>
  </div>
  @endif

  {{-- FORM CARD --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden mb-12">
    <div class="bg-[#5c8a06] px-8 py-4">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">📝 Formulir Buat Program Penyaluran</h3>
    </div>

    <form action="{{ route('penyaluran.store') }}" method="POST" class="p-8 space-y-6">
      @csrf

      @if ($errors->any())
      <div class="bg-rose-50 border-2 border-rose-300 text-rose-800 p-4 rounded-xl">
        <p class="font-black text-xs uppercase mb-2">⚠️ Terjadi Kesalahan Input:</p>
        <ul class="list-disc list-inside space-y-1">
          @foreach ($errors->all() as $error)
          <li class="text-xs font-semibold">{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Judul --}}
        <div class="md:col-span-2">
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Judul Program <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            name="judul"
            value="{{ old('judul') }}"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:ring-0 outline-none transition-colors @error('judul') border-rose-400 bg-rose-50 @enderror"
            placeholder="Contoh: Renovasi Rumah Fakir Miskin">
          @error('judul')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="md:col-span-2">
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Deskripsi Lengkap <span class="text-rose-500">*</span>
          </label>
          <textarea
            name="deskripsi"
            rows="3"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none transition-colors @error('deskripsi') border-rose-400 bg-rose-50 @enderror"
            placeholder="Jelaskan detail program secara lengkap...">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Tanggal --}}
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
          </label>
          <input
            type="date"
            name="tanggal_pelaksanaan"
            value="{{ old('tanggal_pelaksanaan') }}"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 focus:border-emerald-500 outline-none transition-colors @error('tanggal_pelaksanaan') border-rose-400 bg-rose-50 @enderror">
          @error('tanggal_pelaksanaan')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Kebutuhan Dana (UI ETH/IDR) --}}
        <div>
          <div class="flex justify-between items-center mb-2">
            <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">
              Kebutuhan Dana <span class="text-rose-500">*</span>
            </label>
            <div class="flex bg-slate-100 p-0.5 rounded-md border border-slate-200">
              <button type="button" id="btnEth" class="px-3 py-1 bg-white shadow-sm rounded text-[10px] font-bold text-slate-700 transition-all">ETH</button>
              <button type="button" id="btnIdr" class="px-3 py-1 rounded text-[10px] font-bold text-slate-400 hover:text-slate-600 transition-all">IDR</button>
            </div>
          </div>
          <div class="flex">
            <span id="labelCurrency" class="inline-flex items-center px-4 rounded-l-lg border border-r-0 border-slate-400 bg-slate-50 text-slate-500 text-sm font-semibold">
              ETH
            </span>
            {{-- Input Hidden (Ini yang dikirim ke Controller Laravel) --}}
            <input type="hidden" name="dana_dibutuhkan" id="danaDibutuhkanReal" value="{{ old('dana_dibutuhkan') }}">
            
            {{-- Input UI (Ini yang dimanipulasi User) --}}
            <input
              type="number"
              step="any"
              id="nominalUI"
              value="{{ old('dana_dibutuhkan') }}"
              required
              class="flex-1 w-full border-2 border-slate-400 rounded-none rounded-r-lg py-3 px-4 text-sm font-mono font-black text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none transition-colors @error('dana_dibutuhkan') border-rose-400 bg-rose-50 @enderror"
              placeholder="0.00000000">
          </div>
          <p id="priceHelper" class="text-[11px] text-emerald-600 mt-2 font-bold bg-emerald-50 px-2 py-1 inline-block rounded">
            Memuat harga...
          </p>
          @error('dana_dibutuhkan')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Bidang --}}
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Bidang Penyaluran <span class="text-rose-500">*</span>
          </label>
          <select
            name="bidang"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-bold text-slate-900 bg-white focus:border-emerald-500 outline-none transition-colors @error('bidang') border-rose-400 bg-rose-50 @enderror">
            <option value="" class="text-slate-400">-- Pilih Bidang --</option>
            <option value="Ekonomi" {{ old('bidang') == 'Ekonomi'     ? 'selected' : '' }}>Ekonomi</option>
            <option value="Pendidikan" {{ old('bidang') == 'Pendidikan'  ? 'selected' : '' }}>Pendidikan</option>
            <option value="Kesehatan" {{ old('bidang') == 'Kesehatan'   ? 'selected' : '' }}>Kesehatan</option>
            <option value="Kemanusiaan" {{ old('bidang') == 'Kemanusiaan' ? 'selected' : '' }}>Kemanusiaan</option>
          </select>
          @error('bidang')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Sumber Dana --}}
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Sumber Dana <span class="text-rose-500">*</span>
          </label>
          <select
            name="sumber_dana"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-bold text-slate-900 bg-white focus:border-emerald-500 outline-none transition-colors @error('sumber_dana') border-rose-400 bg-rose-50 @enderror">
            <option value="">-- Pilih Sumber --</option>
            <option value="Dana Zakat" {{ old('sumber_dana') == 'Dana Zakat' ? 'selected' : '' }}>Dana Zakat</option>
            <option value="Dana Infak" {{ old('sumber_dana') == 'Dana Infak' ? 'selected' : '' }}>Dana Infak</option>
          </select>
          @error('sumber_dana')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Asnaf --}}
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Asnaf <span class="text-rose-500">*</span>
          </label>
          <select
            name="asnaf"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-bold text-slate-900 bg-white focus:border-emerald-500 outline-none transition-colors @error('asnaf') border-rose-400 bg-rose-50 @enderror">
            <option value="">-- Pilih Asnaf --</option>
            <option value="Fakir" {{ old('asnaf') == 'Fakir'       ? 'selected' : '' }}>Fakir</option>
            <option value="Miskin" {{ old('asnaf') == 'Miskin'      ? 'selected' : '' }}>Miskin</option>
            <option value="Fisabilillah" {{ old('asnaf') == 'Fisabilillah'? 'selected' : '' }}>Fisabilillah</option>
          </select>
          @error('asnaf')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Bentuk Bantuan --}}
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
            Bentuk Bantuan <span class="text-rose-500">*</span>
          </label>
          <input
            type="text"
            name="bentuk_bantuan"
            value="{{ old('bentuk_bantuan') }}"
            required
            class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none transition-colors @error('bentuk_bantuan') border-rose-400 bg-rose-50 @enderror"
            placeholder="Contoh: Paket Sembako">
          @error('bentuk_bantuan')
          <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Tipe Mustahik --}}
        <div class="md:col-span-2 bg-slate-50 p-6 rounded-xl border-2 border-slate-300">
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-4">
            Jenis Data Mustahik <span class="text-rose-500">*</span>
          </label>
          <div class="flex flex-col md:flex-row gap-6">
            <label class="flex items-center gap-3 cursor-pointer group">
              <input
                type="radio"
                name="tipe_mustahik"
                value="umum"
                {{ old('tipe_mustahik', 'umum') == 'umum' ? 'checked' : '' }}
                onclick="toggleMustahikTable(false)"
                class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
              <span class="text-sm font-black text-slate-700 group-hover:text-slate-900 transition-colors">
                Umum <span class="font-normal text-slate-500">(Deskripsi)</span>
              </span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <input
                type="radio"
                name="tipe_mustahik"
                value="detail"
                {{ old('tipe_mustahik') == 'detail' ? 'checked' : '' }}
                onclick="toggleMustahikTable(true)"
                class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
              <span class="text-sm font-black text-slate-700 group-hover:text-slate-900 transition-colors">
                Detail <span class="font-normal text-slate-500">(Tabel NIK & Nama)</span>
              </span>
            </label>
          </div>
        </div>

      </div>{{-- end grid --}}

      {{-- Tabel Mustahik Detail --}}
      <div id="mustahikSection" class="{{ old('tipe_mustahik') == 'detail' ? '' : 'hidden' }} space-y-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest underline decoration-amber-500 decoration-2">
            Daftar Rincian Penerima Manfaat
          </h4>
          <button
            type="button"
            onclick="addMustahikRow()"
            class="bg-amber-500 hover:bg-amber-600 text-white font-black py-2 px-5 rounded-lg text-[10px] uppercase shadow-md transition-all active:scale-95">
            ➕ Tambah Data Mustahik
          </button>
        </div>
        <div class="overflow-x-auto border-2 border-slate-300 rounded-xl shadow-sm">
          <table class="w-full text-left border-collapse bg-white">
            <thead class="bg-slate-800 text-[10px] font-black text-white uppercase">
              <tr>
                <th class="px-4 py-3 w-12 text-center">No</th>
                <th class="px-4 py-3">NIK</th>
                <th class="px-4 py-3">Nama Lengkap</th>
                <th class="px-4 py-3">Bantuan</th>
                <th class="px-4 py-3">Alamat</th>
                <th class="px-4 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody id="mustahikTableBody" class="divide-y-2 divide-slate-100">
            </tbody>
          </table>
        </div>
      </div>

      {{-- Deskripsi Mustahik --}}
      <div class="space-y-2">
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">
          Deskripsi Umum Mustahik <span class="text-rose-500">*</span>
        </label>
        <textarea
          name="deskripsi_mustahik"
          rows="2"
          required
          class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none transition-colors @error('deskripsi_mustahik') border-rose-400 bg-rose-50 @enderror"
          placeholder="Keterangan singkat mengenai target penerima manfaat...">{{ old('deskripsi_mustahik') }}</textarea>
        @error('deskripsi_mustahik')
        <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Submit --}}
      <div class="pt-6 border-t-2 border-slate-200 flex justify-end">
        <button
          type="submit"
          class="bg-[#5c8a06] hover:bg-[#4a6f05] text-white font-black py-4 px-14 rounded-xl shadow-lg transition-all active:scale-95 text-xs uppercase tracking-widest">
          🚀 Submit Pengajuan Program
        </button>
      </div>

    </form>
  </div>

  @push('scripts')
  <script>
    // ==========================================
    // LOGIKA TABEL MUSTAHIK (TIDAK ADA YANG DIUBAH)
    // ==========================================
    let mustahikCount = 0;

    function toggleMustahikTable(show) {
      const section = document.getElementById('mustahikSection');
      const tbody = document.getElementById('mustahikTableBody');
      if (show) {
        section.classList.remove('hidden');
        if (tbody.children.length === 0) addMustahikRow();
      } else {
        section.classList.add('hidden');
      }
    }

    function addMustahikRow() {
      const tbody = document.getElementById('mustahikTableBody');
      const row = document.createElement('tr');
      row.className = "hover:bg-emerald-50 transition-colors";
      row.id = `row-${mustahikCount}`;

      row.innerHTML = `
        <td class="px-4 py-3 text-center font-black text-slate-600 text-xs row-number"></td>
        <td class="px-2 py-2">
          <input type="text" name="mustahik[${mustahikCount}][nik]" required
            class="w-full border-2 border-slate-300 rounded-lg p-2 text-xs font-mono font-bold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none"
            placeholder="16 Digit NIK">
        </td>
        <td class="px-2 py-2">
          <input type="text" name="mustahik[${mustahikCount}][nama]" required
            class="w-full border-2 border-slate-300 rounded-lg p-2 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none"
            placeholder="Nama Lengkap">
        </td>
        <td class="px-2 py-2">
          <input type="text" name="mustahik[${mustahikCount}][bantuan]" required
            class="w-full border-2 border-slate-300 rounded-lg p-2 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none"
            placeholder="Jenis Bantuan">
        </td>
        <td class="px-2 py-2">
          <input type="text" name="mustahik[${mustahikCount}][alamat]" required
            class="w-full border-2 border-slate-300 rounded-lg p-2 text-xs font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none"
            placeholder="Domisili">
        </td>
        <td class="px-4 py-3 text-center">
          <button type="button" onclick="removeMustahikRow(${mustahikCount})"
            class="text-rose-500 hover:text-rose-700 font-black text-sm transition-colors">✖</button>
        </td>
      `;

      tbody.appendChild(row);
      mustahikCount++;
      reindexRows();
    }

    function removeMustahikRow(id) {
      const tbody = document.getElementById('mustahikTableBody');
      if (tbody.children.length > 1) {
        document.getElementById(`row-${id}`).remove();
        reindexRows();
      } else {
        alert("Minimal satu data mustahik wajib diisi.");
      }
    }

    function reindexRows() {
      document.querySelectorAll('#mustahikTableBody tr').forEach((row, index) => {
        row.querySelector('.row-number').innerText = index + 1;
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      const checked = document.querySelector('input[name="tipe_mustahik"]:checked');
      if (checked && checked.value === 'detail') toggleMustahikTable(true);
    });

    // ==========================================
    // ✅ LOGIKA KALKULASI REAL-TIME IDR/ETH (MENGGUNAKAN SERVICE LARAVEL)
    // ==========================================
    let currentCurrency = "ETH";
    
    // ✅ Mengambil harga yang disuntikkan dari PenyaluranController
    // Harga ini sudah di-cache 5 menit oleh backend, mencegah limit API CoinGecko!
    let ethPriceInIdr = {{ isset($ethPrice) ? $ethPrice : 50000000 }}; 

    const nominalUI = document.getElementById('nominalUI');
    const danaDibutuhkanReal = document.getElementById('danaDibutuhkanReal');
    const priceHelper = document.getElementById('priceHelper');
    const btnEth = document.getElementById('btnEth');
    const btnIdr = document.getElementById('btnIdr');
    const labelCurrency = document.getElementById('labelCurrency');

    // 1. Fungsi Hitung Dinamis
    function calculateRealTime() {
      const val = parseFloat(nominalUI.value) || 0;
      
      // Jika kosong
      if (val === 0) {
        priceHelper.innerText = `1 ETH ≈ Rp ${ethPriceInIdr.toLocaleString('id-ID')}`;
        danaDibutuhkanReal.value = ''; // Kosongkan hidden input
        return;
      }
      
      // Jika mode ETH
      if (currentCurrency === 'ETH') {
        const idr = val * ethPriceInIdr;
        priceHelper.innerText = `Setara: Rp ${idr.toLocaleString('id-ID')} (Data Ter-Cache)`;
        danaDibutuhkanReal.value = val; // Nilai murni ETH disimpan ke form backend
      } 
      // Jika mode IDR
      else {
        const eth = val / ethPriceInIdr;
        priceHelper.innerText = `Setara: ${eth.toFixed(6)} ETH (Data Ter-Cache)`;
        danaDibutuhkanReal.value = eth.toFixed(8); // Hasil konversi ETH disimpan ke form backend
      }
    }

    // 2. Langsung jalankan saat halaman dimuat (jika ada form old value)
    calculateRealTime();

    // 3. Pasang Event Listener saat diketik
    nominalUI.addEventListener('input', calculateRealTime);

    // 4. Tombol Toggle Mode Rupiah
    btnIdr.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentCurrency === 'IDR') return;
      
      currentCurrency = 'IDR';
      btnIdr.classList.add('bg-white', 'shadow-sm', 'text-slate-700');
      btnEth.classList.remove('bg-white', 'shadow-sm', 'text-slate-700');
      labelCurrency.innerText = 'Rp';
      
      // Ubah value di UI menyesuaikan mode baru
      if (danaDibutuhkanReal.value) {
          nominalUI.value = Math.round(parseFloat(danaDibutuhkanReal.value) * ethPriceInIdr);
      }
      calculateRealTime();
    });

    // 5. Tombol Toggle Mode Ethereum
    btnEth.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentCurrency === 'ETH') return;
      
      currentCurrency = 'ETH';
      btnEth.classList.add('bg-white', 'shadow-sm', 'text-slate-700');
      btnIdr.classList.remove('bg-white', 'shadow-sm', 'text-slate-700');
      labelCurrency.innerText = 'ETH';
      
      // Ubah value di UI menyesuaikan mode baru
      if (danaDibutuhkanReal.value) {
          nominalUI.value = parseFloat(danaDibutuhkanReal.value);
      }
      calculateRealTime();
    });
  </script>
  @endpush
</x-layouts.admin>