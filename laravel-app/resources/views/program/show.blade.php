{{-- resources/views/program/show.blade.php --}}
<x-layouts.portal title="{{ $program->judul }}">

  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10 min-h-screen">

    {{-- Judul --}}
    <h1 class="text-base font-bold text-gray-800 mb-5 leading-snug">
      {{ $program->judul }}
    </h1>

    {{-- ── CARD UTAMA (semua konten dalam 1 card) ──────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

      {{-- ── CAROUSEL FOTO ──────────────────────────────────────────── --}}
      @if(!empty($program->foto_urls) && count($program->foto_urls) > 0)
      <div class="relative w-full bg-gray-100 select-none overflow-hidden" style="aspect-ratio: 16/8;">

        {{-- Track --}}
        <div id="carousel-track" class="flex h-full" style="transition: transform 0.4s ease;">
          @foreach($program->foto_urls as $foto)
            <div class="min-w-full h-full flex-shrink-0">
              <img src="{{ $foto }}" alt="Foto program" class="w-full h-full object-cover">
            </div>
          @endforeach
        </div>

        {{-- Prev --}}
        <button onclick="carouselPrev()" aria-label="Sebelumnya"
          class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center text-xl font-bold transition-all backdrop-blur-sm">
          ‹
        </button>

        {{-- Next --}}
        <button onclick="carouselNext()" aria-label="Berikutnya"
          class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/30 hover:bg-black/50 text-white flex items-center justify-center text-xl font-bold transition-all backdrop-blur-sm">
          ›
        </button>

        {{-- Dots --}}
        @if(count($program->foto_urls) > 1)
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
          @foreach($program->foto_urls as $i => $foto)
            <button onclick="carouselGoTo({{ $i }})" aria-label="Slide {{ $i+1 }}"
              class="carousel-dot rounded-full transition-all duration-300 {{ $i === 0 ? 'w-5 h-2 bg-white' : 'w-2 h-2 bg-white/40' }}">
            </button>
          @endforeach
        </div>
        @endif

      </div>
      @endif

      {{-- ── BODY CARD ───────────────────────────────────────────────── --}}
      <div class="divide-y divide-gray-100">

        {{-- Blok: Info Program --}}
        <div class="px-6 py-5">
          <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
            Informasi Program Penyaluran
          </h2>
          <div class="space-y-3 text-sm">
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0">Tanggal pelaksanaan</span>
              <span class="text-gray-400 shrink-0">:</span>
              <span class="text-gray-800 font-semibold">
                {{ \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->translatedFormat('j/n/Y') }}
              </span>
            </div>
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0">Bidang penyaluran</span>
              <span class="text-gray-400 shrink-0">:</span>
              <span class="text-gray-800 font-semibold">{{ $program->bidang }}</span>
            </div>
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0">Anggaran dana</span>
              <span class="text-gray-400 shrink-0">:</span>
              <span class="text-emerald-700 font-bold">
                Rp {{ number_format($program->dana_idr, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>

        {{-- Blok: Info Penerima --}}
        <div class="px-6 py-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest">
              Informasi Penerima Bantuan
            </h2>
            @if($program->mustahiks->count() > 0)
              <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                {{ $program->mustahiks->count() }} Penerima Manfaat
              </span>
            @endif
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0">Deskripsi Mustahik</span>
              <span class="text-gray-400 shrink-0">:</span>
              <span class="text-gray-800">{{ $program->deskripsi_mustahik }}</span>
            </div>
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0">Bentuk bantuan</span>
              <span class="text-gray-400 shrink-0">:</span>
              <span class="text-gray-800">{{ $program->bentuk_bantuan }}</span>
            </div>
            @if($program->mustahiks->count() > 0)
            <div class="flex gap-3">
              <span class="w-40 text-gray-400 shrink-0 pt-1">Nama Penerima</span>
              <span class="text-gray-400 shrink-0 pt-1">:</span>
              <div class="flex flex-wrap gap-2 pt-0.5">
                @foreach($program->mustahiks as $mustahik)
                  <span class="text-xs bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-full border border-slate-200">
                    {{ $mustahik->nama }}
                  </span>
                @endforeach
              </div>
            </div>
            @endif
          </div>
        </div>

        {{-- Blok: Deskripsi --}}
        <div class="px-6 py-5">
          <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
            Deskripsi
          </h2>
          <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
            {{ $program->deskripsi }}
          </p>
        </div>

        {{-- Blok: Bukti Blockchain (hanya jika ada tx_hash) --}}
        @if($program->tx_hash)
        <div class="px-6 py-5 bg-emerald-50/50">
          <h2 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
            Bukti Pencairan Blockchain
          </h2>
          <a href="https://sepolia.etherscan.io/tx/{{ $program->tx_hash }}"
             target="_blank"
             class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-emerald-200 rounded-xl text-xs font-mono text-emerald-700 hover:bg-emerald-50 transition-colors shadow-sm">
            <span>{{ substr($program->tx_hash, 0, 12) }}...{{ substr($program->tx_hash, -8) }}</span>
            <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
          </a>
          <p class="text-[10px] text-gray-400 mt-2">
            Tercatat permanen di jaringan Ethereum Sepolia.
          </p>
        </div>
        @endif

      </div>
    </div>

    {{-- Tombol kembali --}}
    <div class="mt-6">
      <a href="{{ route('program.index') }}"
         class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-600 font-semibold transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Program Penyaluran
      </a>
    </div>

  </div>

  {{-- ── SCRIPT CAROUSEL (manual only, no autoplay) ──────────────── --}}
  @push('scripts')
  <script>
    let currentSlide = 0;
    const totalSlides = {{ count($program->foto_urls) }};

    function carouselGoTo(index) {
      currentSlide = (index + totalSlides) % totalSlides;
      document.getElementById('carousel-track').style.transform = `translateX(-${currentSlide * 100}%)`;

      document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
        const isActive = i === currentSlide;
        dot.classList.toggle('w-5',   isActive);
        dot.classList.toggle('h-2',   isActive);
        dot.classList.toggle('bg-white',   isActive);
        dot.classList.toggle('w-2',   !isActive);
        dot.classList.toggle('bg-white/40', !isActive);
      });
    }

    function carouselNext() { carouselGoTo(currentSlide + 1); }
    function carouselPrev() { carouselGoTo(currentSlide - 1); }
  </script>
  @endpush

</x-layouts.portal>