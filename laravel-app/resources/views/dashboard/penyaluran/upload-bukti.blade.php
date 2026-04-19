{{-- resources/views/dashboard/penyaluran/upload-bukti.blade.php --}}

<x-layouts.admin title="Upload Bukti Pelaksanaan">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Upload Bukti Pelaksanaan</h1>
      <p class="text-slate-500 text-xs font-semibold mt-1">Foto akan disimpan permanen di jaringan IPFS (tidak bisa dihapus)</p>
    </div>
    <a href="{{ route('penyaluran.konfirmasi') }}"
      class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase transition-all flex items-center gap-2">
      ← Kembali
    </a>
  </div>

  {{-- Info Program --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden mb-6">
    <div class="bg-slate-800 px-8 py-4">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">📋 Informasi Program</h3>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Judul Program</label>
        <p class="text-sm font-black text-slate-900">{{ $program->judul }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bidang</label>
        <p class="text-sm font-bold text-slate-700">{{ $program->bidang }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Dana Dicairkan</label>
        <p class="text-sm font-black text-emerald-600 font-mono">{{ number_format($program->dana_dibutuhkan, 8) }} ETH</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Pelaksanaan</label>
        <p class="text-sm font-bold text-slate-700">{{ $program->tanggal_pelaksanaan->format('d/m/Y') }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Asnaf</label>
        <p class="text-sm font-bold text-slate-700">{{ $program->asnaf }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tx Hash Blockchain</label>
        <a href="https://sepolia.etherscan.io/tx/{{ $program->tx_hash }}" target="_blank"
          class="text-xs font-mono font-bold text-blue-600 hover:underline">
          {{ substr($program->tx_hash, 0, 16) }}...
        </a>
      </div>
    </div>
  </div>

  {{-- Form Upload --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
    <div class="bg-[#5c8a06] px-8 py-4">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">📸 Upload Foto Bukti Pelaksanaan</h3>
    </div>

    <form action="{{ route('penyaluran.upload.bukti.store', $program->id) }}"
      method="POST"
      enctype="multipart/form-data"
      class="p-8 space-y-6">
      @csrf

      @if(session('error'))
      <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl">
        <p class="font-black text-xs">{{ session('error') }}</p>
      </div>
      @endif

      @if($errors->any())
      <div class="bg-rose-50 border-2 border-rose-300 text-rose-800 p-4 rounded-xl">
        <p class="font-black text-xs uppercase mb-2">⚠️ Kesalahan:</p>
        <ul class="space-y-1">
          @foreach($errors->all() as $error)
          <li class="text-xs font-semibold">{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Dropzone Upload --}}
      <div>
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
          Foto Bukti Pelaksanaan <span class="text-rose-500">*</span>
        </label>

        {{-- Area Drop --}}
        <div id="dropZone"
          class="relative border-2 border-dashed border-slate-400 rounded-xl p-10 text-center cursor-pointer hover:border-emerald-500 hover:bg-emerald-50 transition-all group mb-4">
          <input
            type="file"
            name="foto_bukti[]"
            id="foto_bukti"
            accept="image/jpg,image/jpeg,image/png,image/webp"
            multiple
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            required>

          {{-- State: Belum ada file --}}
          <div id="dropPlaceholder">
            <div class="text-4xl mb-3">📸</div>
            <p class="text-sm font-black text-slate-700">Drag & drop beberapa foto di sini</p>
            <p class="text-xs text-slate-500 font-semibold mt-1">atau klik untuk memilih file (Bisa pilih lebih dari 1)</p>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-3">
              JPG, PNG, WEBP — Maks. 5MB per file
            </p>
          </div>
        </div>

        {{-- State: Preview File List (GRID GALERI) --}}
        <div id="previewGrid" class="hidden grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6 p-4 bg-slate-50 border-2 border-slate-200 rounded-xl shadow-inner">
            <!-- JavaScript akan memasukkan card foto di sini -->
        </div>

        @error('foto_bukti')
        <p class="text-rose-600 text-[10px] font-bold mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Catatan Tambahan --}}
      <div>
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">
          Catatan Pelaksanaan <span class="text-slate-400 font-normal normal-case">(opsional)</span>
        </label>
        <textarea
          name="catatan"
          rows="3"
          class="w-full border-2 border-slate-400 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 placeholder-slate-400 focus:border-emerald-500 outline-none transition-colors"
          placeholder="Contoh: Program berjalan lancar, 25 KK menerima bantuan...">{{ old('catatan') }}</textarea>
      </div>

      {{-- Warning IPFS --}}
      <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-4 flex gap-3">
        <span class="text-xl">⚠️</span>
        <div>
          <p class="text-xs font-black text-amber-800 uppercase tracking-widest mb-1">Perhatian — Data Permanen</p>
          <p class="text-xs font-semibold text-amber-700">
            Foto yang diupload akan disimpan di jaringan IPFS dan <strong>tidak dapat dihapus</strong>.
            Pastikan foto yang diupload adalah foto pelaksanaan program yang benar.
          </p>
        </div>
      </div>

      {{-- Submit --}}
      <div class="pt-4 border-t-2 border-slate-200 flex justify-end gap-4">
        <a href="{{ route('penyaluran.konfirmasi') }}"
          class="border-2 border-slate-300 text-slate-600 font-black py-3 px-8 rounded-xl text-xs uppercase tracking-widest hover:bg-slate-50 transition-all">
          Batal
        </a>
        <button
          type="submit"
          id="btnUpload"
          class="bg-[#5c8a06] hover:bg-[#4a6f05] text-white font-black py-3 px-12 rounded-xl shadow-lg transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-2">
          <span id="btnUploadIcon">🚀</span>
          <span id="btnUploadText">Upload ke IPFS</span>
        </button>
      </div>

    </form>
  </div>

  @push('scripts')
  <script>
    const input = document.getElementById('foto_bukti');
    const dropZone = document.getElementById('dropZone');
    const placeholder = document.getElementById('dropPlaceholder');
    const previewGrid = document.getElementById('previewGrid');
    const btnUpload = document.getElementById('btnUpload');
    const btnText = document.getElementById('btnUploadText');
    const btnIcon = document.getElementById('btnUploadIcon');

    // Keranjang penyimpanan file sementara yang bisa dimanipulasi
    let dataTransfer = new DataTransfer();

    // Fungsi Utama: Sinkronisasi UI dan Input File
    function updateUI() {
        // Kosongkan grid terlebih dahulu
        previewGrid.innerHTML = '';

        if (dataTransfer.files.length === 0) {
            placeholder.classList.remove('hidden');
            previewGrid.classList.add('hidden');
            input.files = dataTransfer.files; 
            input.required = true; // Wajib diisi jika kosong
            return;
        }

        // Tampilkan grid, sembunyikan placeholder
        placeholder.classList.add('hidden');
        previewGrid.classList.remove('hidden');
        input.required = false; // Sudah ada file di dt, aman dari validasi required browser

        // Looping setiap file di dalam keranjang
        Array.from(dataTransfer.files).forEach((file, index) => {
            // Gunakan FileReader untuk membuat URL gambar lokal (Preview)
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (e) => {
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                
                // Buat elemen Card
                const card = document.createElement('div');
                card.className = 'relative bg-white border-2 border-slate-200 rounded-xl p-2 shadow-sm flex flex-col group';
                
                // Isi Card dengan gambar, nama, ukuran, dan tombol silang
                card.innerHTML = `
                    <button type="button" class="btn-remove absolute top-3 right-3 bg-white/90 backdrop-blur rounded-lg w-8 h-8 flex items-center justify-center shadow-md border border-slate-200 hover:bg-rose-500 hover:text-white hover:border-rose-500 text-slate-500 font-bold transition-all z-10 opacity-100 lg:opacity-0 group-hover:opacity-100" data-name="${file.name}">
                        ✕
                    </button>
                    <div class="w-full h-32 bg-slate-100 rounded-lg mb-3 overflow-hidden border border-slate-100">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                    </div>
                    <div class="px-1 pb-1">
                        <p class="text-[11px] font-black text-slate-700 truncate" title="${file.name}">${file.name}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">Ukuran : <span class="text-slate-600">${fileSizeMB} MB</span></p>
                    </div>
                `;
                previewGrid.appendChild(card);
            };
        });

        // Sinkronisasi data Transfer ke input asli agar terkirim ke Laravel
        input.files = dataTransfer.files;
    }

    // Event: Saat file dipilih lewat dialog "Choose File"
    input.addEventListener('change', function() {
        // Tambahkan file baru ke dalam keranjang
        for (let file of this.files) {
            // Cek duplikasi (Jangan masukkan file dengan nama yang sama)
            const isDuplicate = Array.from(dataTransfer.files).some(f => f.name === file.name);
            if (!isDuplicate) {
                dataTransfer.items.add(file);
            }
        }
        updateUI();
    });

    // Drag & Drop Styling
    dropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      dropZone.classList.add('border-emerald-500', 'bg-emerald-50');
    });

    dropZone.addEventListener('dragleave', () => {
      e.preventDefault();
      dropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
    });

    // Event: Saat file di Drop
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropZone.classList.remove('border-emerald-500', 'bg-emerald-50');
      
      for (let file of e.dataTransfer.files) {
          if (file.type.startsWith('image/')) {
              const isDuplicate = Array.from(dataTransfer.files).some(f => f.name === file.name);
              if (!isDuplicate) {
                  dataTransfer.items.add(file);
              }
          }
      }
      updateUI();
    });

    // Event: Saat tombol X (Remove) ditekan
    previewGrid.addEventListener('click', function(e) {
        // Cari tombol terdekat yang memiliki class btn-remove
        const btn = e.target.closest('.btn-remove');
        if (btn) {
            const fileNameToRemove = btn.getAttribute('data-name');
            
            // Buat keranjang baru
            const newDataTransfer = new DataTransfer();
            
            // Masukkan semua file kecuali yang dihapus
            for (let file of dataTransfer.files) {
                if (file.name !== fileNameToRemove) {
                    newDataTransfer.items.add(file);
                }
            }
            
            // Ganti keranjang lama dengan yang baru
            dataTransfer = newDataTransfer;
            
            // Render ulang UI
            updateUI();
        }
    });

    // Loading state saat form di-submit
    document.querySelector('form').addEventListener('submit', function(e) {
      if(dataTransfer.files.length === 0) {
          e.preventDefault();
          alert("Silakan masukkan minimal 1 foto bukti pelaksanaan!");
          return;
      }
      btnUpload.disabled = true;
      btnUpload.classList.add('opacity-75', 'cursor-not-allowed');
      btnIcon.innerText = '⏳';
      btnText.innerText = 'Mengupload ke IPFS...';
    });
  </script>
  @endpush
</x-layouts.admin>