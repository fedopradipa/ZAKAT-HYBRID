{{-- resources/views/program/index.blade.php --}}
<x-layouts.portal title="Program Penyaluran">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-screen">

    {{-- Header --}}
    <h1 class="text-xl font-bold text-emerald-600 text-center mb-8 tracking-tight">
      Program Penyaluran
    </h1>

    {{-- Grid Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($programs as $program)
        <a href="{{ route('program.show', $program->id) }}"
           class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">

          {{-- Thumbnail --}}
          <div class="h-44 bg-gray-100 overflow-hidden">
            @if(!empty($program->thumbnail))
              <img src="{{ $program->thumbnail }}"
                   alt="{{ $program->judul }}"
                   class="w-full h-full object-cover">
            @else
              <div class="w-full h-full flex items-center justify-center bg-slate-100">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
            @endif
          </div>

          {{-- Konten --}}
          <div class="p-4 flex flex-col flex-1">
            <h2 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mb-2">
              {{ $program->judul }}
            </h2>
            <p class="text-xs text-gray-500 line-clamp-3 flex-1 leading-relaxed">
              {{ $program->deskripsi }}
            </p>

            {{-- Footer Card --}}
            <div class="mt-4 flex items-center justify-between text-[11px] text-gray-400">
              <span class="flex items-center gap-1">
                {{-- Logo BAZNAS kecil --}}
                <span class="text-emerald-600 font-bold">🦅</span>
                <span class="font-semibold text-gray-500">BAZNAS</span>
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->translatedFormat('j/n/Y') }}
              </span>
            </div>
          </div>

        </a>
      @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
          <p class="font-semibold">Belum ada program penyaluran yang terlaksana.</p>
        </div>
      @endforelse
    </div>

  </div>

</x-layouts.portal>