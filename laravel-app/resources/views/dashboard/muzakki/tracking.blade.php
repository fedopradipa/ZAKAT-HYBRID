{{-- resources/views/dashboard/muzakki/tracking.blade.php --}}

<x-layouts.portal title="Lacak Penyaluran Dana">
  <div class="max-w-5xl mx-auto px-4 py-10 space-y-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          Live Tracking
        </div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Lacak Penyaluran Dana</h1>
        <p class="text-slate-500 text-sm font-semibold mt-1">
          Pantau aliran dana zakat Anda hingga ke tangan penerima manfaat (Mustahik).
        </p>
      </div>
      <a href="{{ route('muzakki.dashboard') }}" class="bg-slate-100 text-slate-600 hover:bg-slate-200 border-2 border-slate-200 font-black py-2.5 px-5 rounded-xl text-xs uppercase tracking-widest transition-all active:scale-95 shadow-sm inline-flex items-center gap-2">
        ← Kembali ke Riwayat
      </a>
    </div>

    {{-- Konten Pelacakan --}}
    <div class="space-y-6 animate-fade-in-up">
      @forelse($fifoAlokasi as $entry)
        <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden transition-all hover:shadow-lg">
          {{-- Header Program --}}
          <div class="bg-[#5c8a06] px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-black uppercase text-xs tracking-widest">
              {{ $entry['program']->judul }}
            </h3>
            <span class="bg-white/20 text-white border border-white/30 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
              {{ $entry['program']->bidang }}
            </span>
          </div>

          <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Kolom Kiri: Info Alokasi --}}
            <div class="lg:col-span-2 space-y-6">
              <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kontribusi Dana Anda</p>
                <div class="flex items-end gap-3">
                  <p class="text-3xl font-black text-emerald-600 font-mono leading-none">
                    {{ number_format($entry['total_kontribusi'], 4) }} <span class="text-lg text-emerald-400">ETH</span>
                  </p>
                </div>
              </div>

              {{-- Progress Bar Porsi --}}
              @php
                $persen = $entry['program']->dana_dibutuhkan > 0
                  ? ($entry['total_kontribusi'] / $entry['program']->dana_dibutuhkan) * 100
                  : 0;
              @endphp
              <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="flex justify-between text-[10px] font-black text-slate-600 mb-2 uppercase tracking-widest">
                  <span>Porsi Dana Anda di Program Ini</span>
                  <span class="text-emerald-600">{{ number_format($persen, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5">
                  <div class="bg-emerald-500 h-2.5 rounded-full transition-all" style="width: {{ min($persen, 100) }}%"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-bold mt-2">
                  *Sisa dana program didukung oleh dompet Muzakki lainnya (Sistem Gotong Royong)
                </p>
              </div>

              {{-- Sumber Transaksi Anda --}}
              <div>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Dana Diambil Dari Setoran Berikut:</p>
                <div class="grid gap-2">
                  @foreach($entry['allocations'] as $alloc)
                  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-l-2 border-emerald-500 pl-3 py-1">
                    <div>
                      <a href="https://sepolia.etherscan.io/tx/{{ $alloc['transaction']->tx_hash }}" target="_blank" class="text-xs font-mono font-bold text-blue-600 hover:underline">
                        Tx: {{ substr($alloc['transaction']->tx_hash, 0, 20) }}...
                      </a>
                      <p class="text-[10px] text-slate-400 mt-0.5 font-semibold">
                        Disetor pada: {{ $alloc['transaction']->created_at->format('d M Y, H:i') }}
                      </p>
                    </div>
                    <span class="font-mono font-black text-slate-700 text-xs mt-1 sm:mt-0">
                      {{ number_format($alloc['amount'], 4) }} ETH
                    </span>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>

            {{-- Kolom Kanan: Bukti Transparansi IPFS --}}
            <div class="bg-slate-50 rounded-xl border-2 border-slate-200 p-5 flex flex-col h-full">
              <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span>📸</span> Bukti Transparansi
              </h4>
              
              <div class="flex-1 flex flex-col justify-center">
                @if($entry['program']->status === 'telah_terkonfirmasi' && !empty($entry['program']->ipfs_hash))
                  @php
                    // EKSTRAKTOR HASH SAPU JAGAT (Otomatis pecah data yang "menyatu")
                    $rawHash = $entry['program']->ipfs_hash;
                    $hashes = [];
                    
                    if (is_array($rawHash)) {
                        $hashes = $rawHash;
                    } else {
                        $decoded = @json_decode($rawHash, true);
                        if (is_array($decoded)) {
                            $hashes = $decoded;
                        } else {
                            // Bersihkan karakter aneh
                            $cleanString = str_replace(['[', ']', '"', "'", "\\"], '', $rawHash);
                            
                            // Jika ada koma, pecah dengan koma
                            if (strpos($cleanString, ',') !== false) {
                                $hashes = array_values(array_filter(array_map('trim', explode(',', $cleanString))));
                            } else {
                                // JIKA BENAR-BENAR MENYATU (misal: Qm123Qm456)
                                // Cari pola panjang Hash IPFS standar (46 Karakter dimulai dari Qm)
                                preg_match_all('/Qm[1-9A-HJ-NP-Za-km-z]{44}/', $cleanString, $matches);
                                if (!empty($matches[0])) {
                                    $hashes = $matches[0];
                                } else {
                                    $hashes = [trim($cleanString)]; // Fallback terakhir
                                }
                            }
                        }
                    }
                  @endphp
                  
                  @if(count($hashes) > 0)
                    <div class="text-center space-y-4">
                      <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl shadow-sm">
                        ✅
                      </div>
                      <div>
                        <p class="text-xs font-black text-slate-800 uppercase tracking-widest">Program Selesai</p>
                        <p class="text-[10px] text-slate-500 font-semibold mt-1">Tim Penyaluran telah mengunggah bukti pelaksanaan ke jaringan IPFS.</p>
                      </div>
                      <button onclick="openMuzakkiGallery({{ $entry['program']->id }})" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-black py-2.5 px-4 rounded-xl text-[10px] uppercase tracking-widest transition-all shadow-md active:scale-95">
                        Lihat {{ count($hashes) }} Foto Dokumentasi
                      </button>
                    </div>
                  @else
                    <div class="text-center opacity-50">
                      <div class="text-3xl mb-2">📄</div>
                      <p class="text-xs font-bold text-slate-600">Dokumentasi Kosong</p>
                    </div>
                  @endif

                @else
                  <div class="text-center p-4">
                    <div class="w-12 h-12 border-4 border-slate-200 border-t-amber-500 rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-xs font-black text-slate-800 uppercase tracking-widest">Sedang Berjalan</p>
                    <p class="text-[10px] text-slate-500 font-semibold mt-1">Program sedang dalam tahap pelaksanaan. Dokumentasi akan segera tersedia.</p>
                  </div>
                @endif
              </div>
            </div>

          </div>
        </div>
      @empty
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-16 text-center shadow-sm">
          <div class="text-6xl mb-4">🕒</div>
          <h2 class="text-xl font-black text-slate-800 mb-2">Dana Sedang Dalam Antrian</h2>
          <p class="text-slate-500 font-medium text-sm max-w-md mx-auto">
            Sistem sedang mencarikan program penyaluran yang paling tepat untuk dana Anda menggunakan algoritma FIFO. Silakan cek kembali nanti.
          </p>
        </div>
      @endforelse
    </div>
  </div>

  {{-- MODAL GALERI IPFS UNTUK MUZAKKI --}}
  <div id="galleryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border-2 border-slate-300 w-full max-w-4xl max-h-[90vh] flex flex-col animate-fade-in-up">
      <div class="bg-violet-900 px-6 py-4 rounded-t-2xl flex justify-between items-center flex-shrink-0 border-b-4 border-violet-700">
        <div>
          <h3 class="text-white font-black uppercase text-xs tracking-widest">📸 Dokumentasi Penyaluran</h3>
          <p id="galleryModalTitle" class="text-violet-200 text-[10px] font-bold mt-0.5"></p>
        </div>
        <button onclick="closeMuzakkiGallery()" class="text-violet-300 hover:text-white font-black text-lg transition-colors">✕</button>
      </div>
      
      <div class="overflow-y-auto flex-1 p-6 bg-slate-50">
        <div id="galleryModalContent" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {{-- Diisi via JavaScript --}}
        </div>
      </div>

      <div class="px-6 py-4 border-t-2 border-slate-200 bg-white flex justify-between items-center flex-shrink-0 rounded-b-2xl">
        <div class="flex items-center gap-2">
            <span class="text-xl">🧊</span>
            <div>
                <p class="text-[10px] text-slate-800 font-black uppercase tracking-widest">Anti-Manipulasi</p>
                <p class="text-[9px] text-slate-500 font-bold">Foto dienkripsi di jaringan IPFS</p>
            </div>
        </div>
        <button onclick="closeMuzakkiGallery()" class="bg-slate-800 text-white font-black py-2.5 px-8 rounded-xl text-xs uppercase tracking-widest hover:bg-slate-700 transition-all active:scale-95 shadow-md">Tutup</button>
      </div>
    </div>
  </div>

  {{-- Injeksi Data ke Javascript --}}
  @foreach($fifoAlokasi as $entry)
    @php
      $prog = $entry['program'];
      $hashes = [];
      
      if ($prog->status === 'telah_terkonfirmasi' && !empty($prog->ipfs_hash)) {
          $rawHash = $prog->ipfs_hash;
          if (is_array($rawHash)) {
              $hashes = $rawHash;
          } else {
              $decoded = @json_decode($rawHash, true);
              if (is_array($decoded)) {
                  $hashes = $decoded;
              } else {
                  $cleanString = str_replace(['[', ']', '"', "'", "\\"], '', $rawHash);
                  if (strpos($cleanString, ',') !== false) {
                      $hashes = array_values(array_filter(array_map('trim', explode(',', $cleanString))));
                  } else {
                      // Regex untuk jaga-jaga kalau datanya nempel kayak perangko
                      preg_match_all('/Qm[1-9A-HJ-NP-Za-km-z]{44}/', $cleanString, $matches);
                      if (!empty($matches[0])) {
                          $hashes = $matches[0];
                      } else {
                          $hashes = [trim($cleanString)];
                      }
                  }
              }
          }
      }
    @endphp
    @if(count($hashes) > 0)
    <script>
      window._muzakkiPrograms = window._muzakkiPrograms || {};
      window._muzakkiPrograms[{{ $prog->id }}] = {
        judul: @json($prog->judul),
        hashes: @json($hashes)
      };
    </script>
    @endif
  @endforeach

  @push('styles')
  <style>
      .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
      @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
  @endpush

  @push('scripts')
  <script>
    const PINATA_GATEWAY = "{{ config('services.pinata.gateway', 'https://gateway.pinata.cloud/ipfs') }}";

    function openMuzakkiGallery(programId) {
      const data = window._muzakkiPrograms?.[programId];
      if (!data || !data.hashes || data.hashes.length === 0) return;

      const modal = document.getElementById('galleryModal');
      const content = document.getElementById('galleryModalContent');
      document.getElementById('galleryModalTitle').innerText = data.judul;

      let html = '';
      data.hashes.forEach((hash, index) => {
        const url = `${PINATA_GATEWAY}/${hash}`;
        html += `
          <div class="bg-white border-2 border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
            <a href="${url}" target="_blank" class="block relative w-full h-48 bg-slate-200 overflow-hidden">
              <img src="${url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='https://placehold.co/600x400/e2e8f0/475569?text=Gagal+Muat+Foto'">
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                <span class="bg-white/95 text-violet-800 font-black text-[10px] uppercase px-4 py-2 rounded-full opacity-0 group-hover:opacity-100 transition-all transform scale-95 group-hover:scale-100 shadow-xl backdrop-blur-sm">Lihat Detail</span>
              </div>
            </a>
            <div class="p-3 bg-white border-t border-slate-100 text-center">
              <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Dokumentasi ${index + 1}</p>
            </div>
          </div>
        `;
      });

      content.innerHTML = html;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.classList.add('overflow-hidden');
    }

    function closeMuzakkiGallery() {
      const modal = document.getElementById('galleryModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.classList.remove('overflow-hidden');
    }

    // Klik area luar untuk menutup
    document.getElementById('galleryModal').addEventListener('click', function (e) {
        if (e.target === this) closeMuzakkiGallery();
    });
  </script>
  @endpush
</x-layouts.portal>