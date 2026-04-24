<aside class="w-64 bg-slate-900 text-slate-300 hidden md:flex flex-col min-h-screen transition-all duration-300 shadow-2xl relative z-10">
  <div class="p-5 border-b border-slate-800">
    <div class="flex items-center gap-3">
      {{-- Logo BAZNAS --}}
      <img src="{{ asset('images/logo_baznas.jpg') }}" alt="BAZNAS" class="w-9 h-9 rounded-lg object-cover">
      <div>
        <span class="text-white font-black tracking-tighter text-lg block leading-none">BAZNAS</span>
        <span class="text-emerald-500 font-bold text-[10px] tracking-widest uppercase">Portal {{ Auth::user()->role }}</span>
      </div>
    </div>
  </div>

  <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

    {{-- MENU KHUSUS: PEMERINTAH --}}
    @if(Auth::user()->role === 'pemerintah')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Gov Menu</p>

    <a href="{{ route('pemerintah.pengumpulan_zis_dskl') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.pengumpulan_zis_dskl') || request()->routeIs('pemerintah.dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Inbox / Pengumpulan --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
      </svg>
      <span class="text-sm font-semibold">Pengumpulan ZIS-DSKL</span>
    </a>

    <a href="{{ route('pemerintah.penyaluran_zis_dskl') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.penyaluran_zis_dskl') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Share / Penyaluran --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
      </svg>
      <span class="text-sm font-semibold">Penyaluran ZIS-DSKL</span>
    </a>

    <a href="{{ route('pemerintah.program_penyaluran') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.program_penyaluran') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Document report --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <span class="text-sm font-semibold">Program Penyaluran</span>
    </a>
    @endif

    {{-- MENU KHUSUS: KEUANGAN --}}
    @if(Auth::user()->role === 'keuangan')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Finance Menu</p>

    <a href="{{ route('keuangan.pengajuan') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('keuangan.pengajuan*') || request()->routeIs('keuangan.review*') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Check badge / Persetujuan --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span class="text-sm font-semibold">Persetujuan Program</span>
    </a>
    @endif

    {{-- MENU KHUSUS: PENYALURAN --}}
    @if(Auth::user()->role === 'penyaluran')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Distribution Menu</p>

    <a href="{{ route('penyaluran.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('penyaluran.dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Collection / Kelola --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
      </svg>
      <span class="text-sm font-semibold">Kelola Program</span>
    </a>

    <a href="{{ route('penyaluran.konfirmasi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('penyaluran.konfirmasi') || request()->routeIs('penyaluran.upload.bukti') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      {{-- Clipboard check / Konfirmasi --}}
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
      </svg>
      <span class="text-sm font-semibold">Konfirmasi Program</span>
    </a>
    @endif

  </nav>

  <div class="p-4 border-t border-slate-800">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-900/20 text-red-400 transition-all text-sm font-bold group">
        {{-- Power / Disconnect --}}
        <svg class="w-4 h-4 shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Disconnect
      </button>
    </form>
  </div>
</aside>