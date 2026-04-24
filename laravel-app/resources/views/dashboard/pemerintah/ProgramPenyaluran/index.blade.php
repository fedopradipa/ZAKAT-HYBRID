<x-layouts.admin title="Program Penyaluran">

  @push('styles')
  <style>
    .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
  @endpush

  <div class="space-y-6 animate-fade-in-up">

    {{-- ── HEADER ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <h1 class="text-xl font-black text-slate-800 tracking-tight">PROGRAM PENYALURAN</h1>
    </div>

    {{-- ── CARD UTAMA ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

      {{-- Filter Status --}}
      <div class="px-6 py-4 bg-emerald-700 flex items-center gap-4">
        <label class="text-xs font-bold text-emerald-100 whitespace-nowrap">
          Status Program Penyaluran
        </label>
        <div class="relative flex-1 max-w-xs">
          <select
            onchange="this.form.submit()"
            form="formFilter"
            name="status"
            class="w-full appearance-none bg-white border border-emerald-300 text-slate-700 font-semibold text-sm rounded-lg px-4 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-emerald-400 cursor-pointer">
            @foreach($statusOptions as $val => $label)
              <option value="{{ $val }}" {{ $filterStatus === $val ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
          <div class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
        {{-- Form tersembunyi untuk submit filter --}}
        <form id="formFilter" method="GET" action="{{ route('pemerintah.program_penyaluran') }}"></form>
      </div>

      {{-- ── TABEL ────────────────────────────────────────────────────── --}}
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest w-10">No</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Nama Program</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Bidang Program</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Tanggal Pelaksanaan</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Alokasi Dana</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Sumber Dana</th>
              <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</th>
              <th class="px-4 py-3 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Data Mustahik</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($programs as $i => $program)
            <tr class="hover:bg-slate-50/50 transition-colors">

              {{-- No --}}
              <td class="px-4 py-3 text-slate-400 text-xs font-mono">
                {{ $i + 1 }}
              </td>

              {{-- Nama Program --}}
              <td class="px-4 py-3 max-w-[200px]">
                <p class="font-semibold text-slate-800 truncate text-xs">{{ $program->judul }}</p>
              </td>

              {{-- Bidang --}}
              <td class="px-4 py-3 text-slate-600 text-xs">
                {{ $program->bidang }}
              </td>

              {{-- Tanggal --}}
              <td class="px-4 py-3 text-slate-600 text-xs">
                {{ \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->format('j/n/Y') }}
              </td>

              {{-- Alokasi Dana --}}
              <td class="px-4 py-3 text-xs">
                <span class="font-bold text-emerald-700">
                  Rp {{ number_format($program->dana_idr, 0, ',', '.') }}
                </span>
              </td>

              {{-- Sumber Dana --}}
              <td class="px-4 py-3 text-xs text-slate-600 capitalize">
                {{ str_replace('_', ' ', $program->sumber_dana) }}
              </td>

              {{-- Status Badge --}}
              <td class="px-4 py-3">
                @php
                  $statusMap = [
                    'belum_cair'          => ['Belum Dicairkan',    'bg-blue-50 text-blue-600 border-blue-200'],
                    'proses_pelaksanaan'  => ['Proses Pelaksanaan', 'bg-amber-50 text-amber-600 border-amber-200'],
                    'tidak_terlaksana'    => ['Tidak Terlaksana',   'bg-red-50 text-red-600 border-red-200'],
                    'belum_dikonfirmasi'  => ['Belum Dikonfirmasi', 'bg-orange-50 text-orange-600 border-orange-200'],
                    'telah_terkonfirmasi' => ['Telah Terkonfirmasi','bg-emerald-50 text-emerald-700 border-emerald-200'],
                  ];
                  $s = $statusMap[$program->status] ?? [$program->status, 'bg-slate-50 text-slate-600 border-slate-200'];
                @endphp
                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $s[1] }}">
                  {{ $s[0] }}
                </span>
              </td>

              {{-- Tombol Data Mustahik --}}
              <td class="px-4 py-3 text-center">
                <button
                  onclick="showMustahik(
                    {{ json_encode($program->bentuk_bantuan) }},
                    {{ json_encode($program->deskripsi_mustahik) }},
                    {{ $program->mustahiks->count() }},
                    {{ json_encode($program->mustahiks->pluck('nama')) }}
                  )"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold rounded-lg transition-all active:scale-95 shadow-sm">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  Lihat
                </button>
              </td>

            </tr>
            @empty
            <tr>
              <td colspan="8" class="px-6 py-14 text-center">
                <div class="mb-2 flex justify-center opacity-40">
                  <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                  </svg>
                </div>
                <p class="text-slate-500 font-semibold text-sm">
                  Tidak ada program dengan status
                  <span class="text-emerald-600">
                    "{{ $statusOptions[$filterStatus] ?? 'dipilih' }}"
                  </span>
                </p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

  {{-- ── MODAL POPUP DATA MUSTAHIK ────────────────────────────────────── --}}
  <div id="modalMustahik"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden"
    onclick="closeMustahikIfOutside(event)">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

      {{-- Header Modal --}}
      <div class="bg-blue-600 px-6 py-4 flex items-center justify-between">
        <h3 class="text-white font-black text-sm">Informasi Data Mustahik</h3>
        <button onclick="closeModal()"
          class="text-white/80 hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      {{-- Body Modal --}}
      <div class="px-6 py-5 space-y-4 text-sm">

        <div class="flex gap-4">
          <span class="w-36 text-slate-500 font-medium shrink-0">Bentuk Bantuan</span>
          <span class="text-slate-400 shrink-0">:</span>
          <span id="modal-bentuk" class="text-slate-800 font-semibold"></span>
        </div>

        <div class="flex gap-4">
          <span class="w-36 text-slate-500 font-medium shrink-0">Deskripsi mustahik</span>
          <span class="text-slate-400 shrink-0">:</span>
          <span id="modal-deskripsi" class="text-slate-800 leading-relaxed"></span>
        </div>

        {{-- Nama penerima (hanya jika ada) --}}
        <div id="modal-nama-wrapper" class="flex gap-4 hidden">
          <span class="w-36 text-slate-500 font-medium shrink-0">Nama Penerima</span>
          <span class="text-slate-400 shrink-0">:</span>
          <div id="modal-nama" class="flex flex-wrap gap-1.5"></div>
        </div>

      </div>

      {{-- Footer Modal --}}
      <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
        <button onclick="closeModal()"
          class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-all active:scale-95">
          Tutup
        </button>
      </div>

    </div>
  </div>

  @push('scripts')
  <script>
    function showMustahik(bentuk, deskripsi, jumlah, namaList) {
      document.getElementById('modal-bentuk').textContent   = bentuk   || '-';
      document.getElementById('modal-deskripsi').textContent = deskripsi || '-';

      const namaWrapper = document.getElementById('modal-nama-wrapper');
      const namaContainer = document.getElementById('modal-nama');
      namaContainer.innerHTML = '';

      if (jumlah > 0 && namaList.length > 0) {
        namaList.forEach(nama => {
          const span = document.createElement('span');
          span.className = 'text-xs bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-full border border-slate-200';
          span.textContent = nama;
          namaContainer.appendChild(span);
        });
        namaWrapper.classList.remove('hidden');
      } else {
        namaWrapper.classList.add('hidden');
      }

      document.getElementById('modalMustahik').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      document.getElementById('modalMustahik').classList.add('hidden');
      document.body.style.overflow = '';
    }

    function closeMustahikIfOutside(event) {
      if (event.target === document.getElementById('modalMustahik')) {
        closeModal();
      }
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModal();
    });
  </script>
  @endpush

</x-layouts.admin>