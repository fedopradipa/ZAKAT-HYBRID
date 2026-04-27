{{-- resources/views/dashboard/penyaluran/konfirmasi.blade.php --}}

<x-layouts.admin title="Konfirmasi Program">
  <div class="mb-8">
    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Status Pengajuan Program</h1>
    <p class="text-slate-500 text-sm font-semibold mt-1">Pantau riwayat, lihat data mustahik, dan upload bukti pelaksanaan program.</p>
  </div>

  @if(session('success'))
  <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-xl shadow-sm flex items-start gap-3">
    <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div>
        <p class="font-black text-sm">Berhasil!</p>
        <p class="text-xs font-semibold mt-0.5">{{ session('success') }}</p>
    </div>
  </div>
  @endif

  @if(session('error'))
  <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 mb-6 rounded-r-xl shadow-sm flex items-start gap-3">
    <svg class="w-6 h-6 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div>
        <p class="font-black text-sm">Gagal!</p>
        <p class="text-xs font-semibold mt-0.5">{{ session('error') }}</p>
    </div>
  </div>
  @endif

  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-sm overflow-hidden">
    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
        <h3 class="text-white font-black uppercase text-xs tracking-widest flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Daftar Program Penyaluran
        </h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b-2 border-slate-200">
          <tr>
            <th class="px-6 py-4 w-12 text-center">No</th>
            <th class="px-6 py-4">Informasi Program & Mustahik</th>
            <th class="px-6 py-4">Dana & Tanggal</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center w-48">Aksi & Bukti</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($programs as $index => $program)
            @php
                // Hitung keterlambatan (hanya untuk yang sedang diproses)
                $tanggalPelaksanaan = \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->startOfDay();
                $isTerlambat = (
                    $program->status === 'proses_pelaksanaan' &&
                    $tanggalPelaksanaan->lessThanOrEqualTo(now()->startOfDay())
                );
            @endphp

            <tr class="{{ ($isTerlambat || $program->status === 'tidak_terlaksana') ? 'bg-rose-50/50' : 'bg-white hover:bg-slate-50' }} transition-colors">
              <td class="px-6 py-4 text-center text-xs font-black text-slate-400">{{ $index + 1 }}</td>
              
              <td class="px-6 py-4">
                {{-- Judul tetap terlihat normal tanpa coretan line-through --}}
                <p class="text-sm font-black text-slate-800">{{ $program->judul }}</p>
                <p class="text-xs font-semibold text-slate-500 mt-0.5 mb-2.5">{{ $program->bidang }} • {{ $program->asnaf }}</p>
                
                {{-- TOMBOL LIHAT MUSTAHIK --}}
                @if($program->tipe_mustahik === 'detail')
                    <button type="button" onclick='openMustahikModal(@json($program->mustahiks), "{{ addslashes($program->judul) }}")' 
                            class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Lihat Data Mustahik ({{ $program->mustahiks->count() }})
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-slate-500 bg-slate-50 border border-slate-200 rounded">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Penerima Umum
                    </span>
                @endif
              </td>
              
              <td class="px-6 py-4">
                <p class="text-sm font-mono font-black text-emerald-600">{{ number_format($program->dana_dibutuhkan, 8) }} ETH</p>
                <p class="flex items-center gap-1.5 text-[11px] font-bold {{ ($isTerlambat || $program->status === 'tidak_terlaksana') ? 'text-rose-600' : 'text-slate-500' }} mt-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  {{ $tanggalPelaksanaan->format('d M Y') }}
                </p>
              </td>
              
              <td class="px-6 py-4 text-center">
                @if($program->status == 'belum_cair')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-slate-600 bg-slate-100 rounded border border-slate-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Menunggu Dana
                    </span>
                @elseif($program->status == 'tidak_terlaksana')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-rose-700 bg-rose-100 rounded border border-rose-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Kadaluarsa (Batal)
                    </span>
                @elseif($program->status == 'proses_pelaksanaan')
                    @if($isTerlambat)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-rose-700 bg-rose-100 rounded border border-rose-200 shadow-sm animate-pulse">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Terlambat Lapor!
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-amber-700 bg-amber-50 rounded border border-amber-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Sedang Dilaksanakan
                        </span>
                    @endif
                @elseif($program->status == 'telah_terkonfirmasi')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Selesai & Tersertifikasi
                    </span>
                @endif
              </td>
              
              <td class="px-6 py-4 text-center">
                <div class="flex flex-col gap-2 items-center justify-center">
                    
                    @if($program->status == 'belum_cair')
                        <span class="text-[10px] font-bold text-slate-400 italic">Menunggu ACC Keuangan</span>
                    
                    @elseif($program->status == 'tidak_terlaksana')
                        <span class="text-[10px] font-bold text-rose-400 italic">Dibatalkan otomatis oleh sistem</span>

                    @elseif($program->status == 'proses_pelaksanaan')
                        <a href="{{ route('penyaluran.upload.bukti', $program->id) }}"
                           class="w-full text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded transition-all active:scale-95 border {{ $isTerlambat ? 'bg-rose-600 hover:bg-rose-700 text-white border-rose-700 shadow-sm' : 'bg-slate-800 hover:bg-slate-700 text-white border-slate-800 shadow-sm' }}">
                            Upload Bukti
                        </a>

                    @elseif($program->status == 'telah_terkonfirmasi')
                        {{-- ⭐ PERBAIKAN TOMBOL BUKTI: Mencegah Syntax Error dengan data-attribute --}}
                        @if(!empty($program->bukti_ipfs_hash))
                            <button type="button" 
                                    data-bukti="{{ is_array($program->bukti_ipfs_hash) ? json_encode($program->bukti_ipfs_hash) : $program->bukti_ipfs_hash }}" 
                                    data-judul="{{ $program->judul }}"
                                    onclick="openGalleryModal(this)"
                                    class="w-full flex justify-center items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest px-4 py-2 rounded transition-all active:scale-95 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Foto Bukti
                            </button>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 italic">Bukti Tidak Tersedia</span>
                        @endif
                        
                        {{-- Tombol Alokasi FIFO Dihapus Sesuai Permintaan --}}
                    @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center opacity-50">
                    <svg class="w-12 h-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-bold text-slate-500">Belum ada program penyaluran.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>


  {{-- ========================================================== --}}
  {{-- MODAL 1: LIHAT DATA MUSTAHIK --}}
  {{-- ========================================================== --}}
  <div id="mustahikModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Daftar Penerima Manfaat (Mustahik)
                </h3>
                <p id="mustahikTitle" class="text-xs font-bold text-slate-500 mt-1"></p>
            </div>
            <button onclick="closeMustahikModal()" class="text-slate-400 hover:text-slate-800 transition-colors p-2 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-0 overflow-y-auto bg-white flex-1">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black sticky top-0 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Bentuk Bantuan</th>
                        <th class="px-4 py-3">Alamat</th>
                    </tr>
                </thead>
                <tbody id="mustahikTableBody" class="divide-y divide-slate-100">
                    <!-- Data Mustahik di-render dari JS -->
                </tbody>
            </table>
        </div>
    </div>
  </div>


  {{-- ========================================================== --}}
  {{-- MODAL 2: GALERI FOTO BUKTI IPFS --}}
  {{-- ========================================================== --}}
  <div id="galleryModal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-slate-50 rounded-2xl border border-slate-200 shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Galeri Bukti Penyaluran (IPFS)
                </h3>
                <p id="galleryTitle" class="text-xs font-bold text-slate-500 mt-1"></p>
            </div>
            <button onclick="closeGalleryModal()" class="text-slate-400 hover:text-slate-800 transition-colors p-2 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="galleryGrid" class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-slate-100 flex-1">
            <!-- Foto IPFS di-render dari JS -->
        </div>
    </div>
  </div>


  {{-- ========================================================== --}}
  {{-- JAVASCRIPT --}}
  {{-- ========================================================== --}}
  @push('scripts')
  <script>
    function openMustahikModal(mustahiks, judul) {
        document.getElementById('mustahikTitle').innerText = judul;
        const tbody = document.getElementById('mustahikTableBody');
        
        if (!mustahiks || mustahiks.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-8 text-center text-slate-500 font-bold text-sm">Tidak ada data mustahik terperinci.</td></tr>`;
        } else {
            tbody.innerHTML = mustahiks.map((m, index) => {
                return `
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 text-center text-xs font-black text-slate-400">${index + 1}</td>
                    <td class="px-4 py-3 text-xs font-mono font-black text-slate-700">${m.nik || '-'}</td>
                    <td class="px-4 py-3 text-xs font-black text-slate-800">${m.nama || '-'}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-slate-600">${m.bentuk_bantuan || '-'}</td>
                    <td class="px-4 py-3 text-xs font-medium text-slate-500">${m.alamat || '-'}</td>
                </tr>`;
            }).join('');
        }

        const modal = document.getElementById('mustahikModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeMustahikModal() {
        const modal = document.getElementById('mustahikModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    // ⭐ FUNGSI BARU YANG JAUH LEBIH KUAT DARI ERROR JSON
    function openGalleryModal(btnElement) {
        const rawHashes = btnElement.getAttribute('data-bukti');
        const judul = btnElement.getAttribute('data-judul');
        
        if(!rawHashes || rawHashes.trim() === '') {
            return alert('Tidak ada foto bukti.');
        }
        
        document.getElementById('galleryTitle').innerText = judul;
        const grid = document.getElementById('galleryGrid');
        
        let hashArray = [];
        try { 
            hashArray = JSON.parse(rawHashes); 
            // Jika terjadi double-encode di dalam database
            if (typeof hashArray === 'string') {
                hashArray = JSON.parse(hashArray);
            }
        } catch(e) { 
            hashArray = [rawHashes]; // Fallback jika cuma string biasa tanpa array bracket
        }
        
        grid.innerHTML = hashArray.map((hash, index) => {
            const url = `https://gateway.pinata.cloud/ipfs/${hash}`;
            return `
            <div class="bg-white rounded-xl overflow-hidden border border-slate-200 shadow-sm group flex flex-col">
                <a href="${url}" target="_blank" class="block relative aspect-[4/3] overflow-hidden bg-slate-200">
                    <img src="${url}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                         alt="Bukti ${index + 1}" 
                         onerror="this.src='https://via.placeholder.com/400x300?text=Memuat+dari+IPFS...'">
                    <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/40 transition-colors flex items-center justify-center">
                        <span class="opacity-0 group-hover:opacity-100 bg-white/95 text-slate-800 text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded shadow-sm transform translate-y-4 group-hover:translate-y-0 transition-all flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            Buka Ukuran Penuh
                        </span>
                    </div>
                </a>
                <div class="p-3 bg-white flex justify-between items-center border-t border-slate-100">
                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Foto Bukti ${index + 1}
                    </p>
                    <a href="${url}" target="_blank" class="text-slate-400 hover:text-violet-600 bg-slate-50 hover:bg-violet-50 p-1.5 rounded transition-colors" title="Buka di Tab Baru">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>`;
        }).join('');

        const modal = document.getElementById('galleryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeGalleryModal() {
        const modal = document.getElementById('galleryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
  </script>
  @endpush
</x-layouts.admin>