{{-- resources/views/dashboard/penyaluran/konfirmasi.blade.php --}}

<x-layouts.admin title="Konfirmasi Program">
  <div class="mb-8">
    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Status Pengajuan Program</h1>
    <p class="text-slate-500 text-sm font-semibold mt-1">Pantau riwayat dan upload bukti pelaksanaan program yang telah disetujui.</p>
  </div>

  @if(session('success'))
  <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-xl shadow-sm">
    <p class="font-black text-sm">✅ Berhasil!</p>
    <p class="text-xs font-semibold">{{ session('success') }}</p>
  </div>
  @endif

  @if(session('error'))
  <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-800 p-4 mb-6 rounded-r-xl shadow-sm">
    <p class="font-black text-sm">❌ Gagal!</p>
    <p class="text-xs font-semibold">{{ session('error') }}</p>
  </div>
  @endif

  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
      <h4 class="text-white font-black uppercase text-[10px] tracking-widest">📋 Daftar Program Penyaluran</h4>
      <span class="bg-emerald-500 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">
        Total: {{ $programs->count() }} Program
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-[10px] font-black text-slate-600 uppercase tracking-widest border-b-2 border-slate-200">
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Nama Program</th>
            <th class="px-5 py-4">Bidang Penyaluran</th>
            <th class="px-5 py-4">Tanggal Pelaksanaan</th>
            <th class="px-5 py-4">Alokasi Dana</th>
            <th class="px-5 py-4 text-center">Status</th>
            <th class="px-5 py-4 text-center">Data Mustahik</th>
            <th class="px-5 py-4 text-center">Bukti IPFS</th>
            <th class="px-5 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-100">
          @forelse($programs as $index => $program)
          <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-emerald-50 transition-colors">

            {{-- No --}}
            <td class="px-5 py-4 text-xs font-black text-slate-500">{{ $index + 1 }}</td>

            {{-- Nama Program --}}
            <td class="px-5 py-4">
              <p class="text-sm font-black text-slate-900 max-w-xs truncate">{{ $program->judul }}</p>
              <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                ID #{{ $program->id }} · {{ $program->created_at->format('d/m/Y') }}
              </p>
            </td>

            {{-- Bidang --}}
            <td class="px-5 py-4">
              <span class="text-xs font-black text-slate-700">{{ $program->bidang }}</span>
              <span class="block text-[10px] text-slate-400 uppercase tracking-tighter mt-0.5">{{ $program->asnaf }}</span>
            </td>

            {{-- Tanggal --}}
            <td class="px-5 py-4 text-xs font-bold text-slate-700">
              {{ $program->tanggal_pelaksanaan->format('d/m/Y') }}
            </td>

            {{-- Dana --}}
            <td class="px-5 py-4">
              <span class="font-mono font-black text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200 text-xs">
                {{ number_format($program->dana_dibutuhkan, 4) }} ETH
              </span>
            </td>

            {{-- Status --}}
            <td class="px-5 py-4 text-center">
              @if($program->status === 'belum_cair')
                <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 border-2 border-slate-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                  🔒 Belum Cair
                </span>
              @elseif($program->status === 'proses_pelaksanaan')
                <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 border-2 border-blue-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Proses Pelaksanaan
                </span>
              @elseif($program->status === 'tidak_terlaksana')
                <span class="inline-flex items-center gap-1.5 bg-rose-100 text-rose-700 border-2 border-rose-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                  ✖ Tidak Terlaksana
                </span>
              @elseif($program->status === 'belum_dikonfirmasi')
                <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-700 border-2 border-amber-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Belum Dikonfirmasi
                </span>
              @elseif($program->status === 'telah_terkonfirmasi')
                <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 border-2 border-emerald-300 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                  ✅ Telah Terkonfirmasi
                </span>
              @endif
            </td>

            {{-- Data Mustahik --}}
            <td class="px-5 py-4 text-center">
              @if($program->tipe_mustahik === 'detail' && $program->mustahiks->isNotEmpty())
                <button
                  onclick="openMustahikModal({{ $program->id }})"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-black py-1.5 px-4 rounded-lg text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm">
                  👥 Lihat
                </button>
              @else
                <span class="text-[10px] text-slate-400 font-bold italic">Umum</span>
              @endif
            </td>

            {{-- Bukti IPFS --}}
            <td class="px-5 py-4 text-center">
              @if($program->ipfs_hash)
                @php
                  $hashes = is_array($program->ipfs_hash)
                    ? $program->ipfs_hash
                    : json_decode($program->ipfs_hash, true) ?? [$program->ipfs_hash];
                @endphp
                <button
                  onclick="openGalleryModal({{ $program->id }})"
                  class="inline-flex items-center gap-1.5 bg-violet-100 text-violet-700 border-2 border-violet-300 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-violet-200 transition-all active:scale-95 shadow-sm">
                  📸 Galeri ({{ count($hashes) }})
                </button>
              @else
                <span class="text-[10px] text-slate-300 font-bold italic">—</span>
              @endif
            </td>

            {{-- Aksi --}}
            <td class="px-5 py-4 text-center">
              <div class="flex flex-col items-center gap-1.5">

                {{-- Tombol Upload Bukti --}}
                @if(in_array($program->status, ['proses_pelaksanaan', 'belum_dikonfirmasi']))
                  <a href="{{ route('penyaluran.upload.bukti', $program->id) }}"
                    class="inline-flex items-center gap-1.5 bg-[#5c8a06] hover:bg-[#4a6f05] text-white font-black py-1.5 px-4 rounded-lg text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm w-full justify-center">
                    📸 Konfirmasi
                  </a>
                @elseif($program->status === 'telah_terkonfirmasi')
                  <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 border-2 border-slate-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest w-full justify-center">
                    ✅ Selesai
                  </span>
                @elseif($program->status === 'tidak_terlaksana')
                  <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-400 border-2 border-rose-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest w-full justify-center">
                    🔄 Refunded
                  </span>
                @else
                  <span class="text-[10px] text-slate-300 font-bold italic">—</span>
                @endif

                {{-- Tombol FIFO: hanya untuk program yang sudah cair --}}
                @if(in_array($program->status, ['proses_pelaksanaan', 'belum_dikonfirmasi', 'telah_terkonfirmasi']))
                  <a href="{{ route('penyaluran.fifo.program', $program->id) }}"
                    class="inline-flex items-center gap-1.5 bg-violet-600 hover:bg-violet-700 text-white font-black py-1.5 px-4 rounded-lg text-[10px] uppercase tracking-widest transition-all active:scale-95 shadow-sm w-full justify-center">
                    📊 FIFO
                  </a>
                @endif

              </div>
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="9" class="px-6 py-20 text-center">
              <div class="text-4xl mb-3">📭</div>
              <p class="text-slate-500 font-black uppercase text-xs tracking-widest">Belum ada data pengajuan program</p>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- MODAL: Data Mustahik --}}
  <div id="mustahikModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border-2 border-slate-300 w-full max-w-3xl max-h-[80vh] flex flex-col">
      <div class="bg-slate-800 px-6 py-4 rounded-t-2xl flex justify-between items-center flex-shrink-0">
        <h3 class="text-white font-black uppercase text-xs tracking-widest">👥 Data Mustahik</h3>
        <button onclick="closeMustahikModal()"
          class="text-slate-400 hover:text-white font-black text-lg transition-colors">✕</button>
      </div>
      <div class="overflow-y-auto flex-1 p-6">
        <div id="mustahikModalContent"></div>
      </div>
      <div class="px-6 py-4 border-t-2 border-slate-100 flex justify-end flex-shrink-0">
        <button onclick="closeMustahikModal()"
          class="border-2 border-slate-300 text-slate-600 font-black py-2 px-8 rounded-xl text-xs uppercase tracking-widest hover:bg-slate-50 transition-all">
          Tutup
        </button>
      </div>
    </div>
  </div>

  {{-- MODAL: Galeri IPFS --}}
  <div id="galleryModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border-2 border-slate-300 w-full max-w-4xl max-h-[85vh] flex flex-col">
      <div class="bg-violet-700 px-6 py-4 rounded-t-2xl flex justify-between items-center flex-shrink-0">
        <div>
          <h3 class="text-white font-black uppercase text-xs tracking-widest">📸 Galeri Bukti Pelaksanaan</h3>
          <p id="galleryModalTitle" class="text-violet-200 text-[10px] font-semibold mt-0.5"></p>
        </div>
        <button onclick="closeGalleryModal()"
          class="text-violet-300 hover:text-white font-black text-lg transition-colors">✕</button>
      </div>
      <div class="overflow-y-auto flex-1 p-6 bg-slate-50">
        <div id="galleryModalContent" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
      </div>
      <div class="px-6 py-4 border-t-2 border-slate-200 bg-white flex justify-between items-center flex-shrink-0 rounded-b-2xl">
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Data tersimpan di jaringan IPFS</p>
        <button onclick="closeGalleryModal()"
          class="bg-slate-200 text-slate-700 font-black py-2 px-8 rounded-xl text-xs uppercase tracking-widest hover:bg-slate-300 transition-all">
          Tutup Galeri
        </button>
      </div>
    </div>
  </div>

  {{-- Injeksi Data ke Javascript --}}
  @foreach($programs as $program)
    @php
      $mustahikList = [];
      if ($program->tipe_mustahik === 'detail' && $program->mustahiks->isNotEmpty()) {
        $mustahikList = $program->mustahiks->map(function($m) {
          return [
            'nik'            => $m->nik,
            'nama'           => $m->nama,
            'bentuk_bantuan' => $m->bentuk_bantuan,
            'alamat'         => $m->alamat,
          ];
        });
      }

      $ipfsHashes = [];
      if ($program->ipfs_hash) {
        $decoded    = is_array($program->ipfs_hash)
          ? $program->ipfs_hash
          : json_decode($program->ipfs_hash, true);
        $ipfsHashes = $decoded ?? [$program->ipfs_hash];
      }
    @endphp
    <script>
      window._programData = window._programData || {};
      window._programData[{{ $program->id }}] = {
        judul: @json($program->judul),
        mustahiks: @json($mustahikList),
        hashes: @json($ipfsHashes)
      };
    </script>
  @endforeach

  @push('scripts')
  <script>
    function openMustahikModal(programId) {
      const data    = window._programData?.[programId];
      const modal   = document.getElementById('mustahikModal');
      const content = document.getElementById('mustahikModalContent');

      if (!data || !data.mustahiks || data.mustahiks.length === 0) return;

      let rows = data.mustahiks.map((m, i) => `
        <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-slate-50'}">
          <td class="px-4 py-3 text-center font-black text-slate-500 text-xs">${i + 1}</td>
          <td class="px-4 py-3 text-xs font-mono font-black text-slate-800">${m.nik}</td>
          <td class="px-4 py-3 text-xs font-black text-slate-900">${m.nama}</td>
          <td class="px-4 py-3 text-xs font-semibold text-slate-700">${m.bentuk_bantuan}</td>
          <td class="px-4 py-3 text-xs font-semibold text-slate-700">${m.alamat}</td>
        </tr>
      `).join('');

      content.innerHTML = `
        <div class="mb-4 flex items-center justify-between">
          <p class="text-sm font-black text-slate-800">${data.judul}</p>
          <span class="bg-emerald-100 text-emerald-800 text-[10px] font-black px-3 py-1 rounded-full border-2 border-emerald-300">
            ${data.mustahiks.length} Mustahik
          </span>
        </div>
        <div class="overflow-x-auto border-2 border-slate-300 rounded-xl">
          <table class="w-full text-left border-collapse bg-white">
            <thead class="bg-slate-800 text-[10px] font-black text-white uppercase">
              <tr>
                <th class="px-4 py-3 w-10 text-center">No</th>
                <th class="px-4 py-3">NIK</th>
                <th class="px-4 py-3">Nama Lengkap</th>
                <th class="px-4 py-3">Bantuan</th>
                <th class="px-4 py-3">Alamat</th>
              </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100">${rows}</tbody>
          </table>
        </div>
      `;

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.classList.add('overflow-hidden');
    }

    function closeMustahikModal() {
      document.getElementById('mustahikModal').classList.add('hidden');
      document.getElementById('mustahikModal').classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }

    const PINATA_GATEWAY = "{{ config('services.pinata.gateway', 'https://gateway.pinata.cloud/ipfs') }}";

    function openGalleryModal(programId) {
      const data    = window._programData?.[programId];
      const modal   = document.getElementById('galleryModal');
      const content = document.getElementById('galleryModalContent');

      if (!data || !data.hashes || data.hashes.length === 0) return;

      document.getElementById('galleryModalTitle').innerText = data.judul;

      content.innerHTML = data.hashes.map((hash, index) => {
        const url = `${PINATA_GATEWAY}/${hash}`;
        return `
          <div class="bg-white border-2 border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <a href="${url}" target="_blank" class="block relative w-full h-48 bg-slate-100 overflow-hidden">
              <img src="${url}" alt="Bukti ${index + 1}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-slate-400 text-xs font-bold\'>Gagal memuat</div>'">
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                <span class="bg-white/90 text-violet-700 font-black text-[10px] uppercase px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                  Buka Resolusi Penuh
                </span>
              </div>
            </a>
            <div class="p-3 bg-white flex justify-between items-center border-t border-slate-100">
              <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Foto Bukti ${index + 1}</p>
              <a href="${url}" target="_blank" class="text-violet-600 hover:text-violet-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
              </a>
            </div>
          </div>
        `;
      }).join('');

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.classList.add('overflow-hidden');
    }

    function closeGalleryModal() {
      document.getElementById('galleryModal').classList.add('hidden');
      document.getElementById('galleryModal').classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('mustahikModal').addEventListener('click', function(e) {
      if (e.target === this) closeMustahikModal();
    });
    document.getElementById('galleryModal').addEventListener('click', function(e) {
      if (e.target === this) closeGalleryModal();
    });
  </script>
  @endpush
</x-layouts.admin>