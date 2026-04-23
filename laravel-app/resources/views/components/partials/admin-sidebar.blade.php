<aside class="w-64 bg-slate-900 text-slate-300 hidden md:flex flex-col min-h-screen transition-all duration-300 shadow-2xl relative z-10">
  <div class="p-6 border-b border-slate-800">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-sm">🦅</div>
      <div>
        <span class="text-white font-black tracking-tighter text-lg block leading-none">BAZNAS</span>
        <span class="text-emerald-500 font-bold text-[10px] tracking-widest uppercase">Portal {{ Auth::user()->role }}</span>
      </div>
    </div>
  </div>

  <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
    
    {{-- ========================================== --}}
    {{-- MENU KHUSUS: PEMERINTAH --}}
    {{-- ========================================== --}}
    @if(Auth::user()->role === 'pemerintah')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Gov Menu</p>

    <a href="{{ route('pemerintah.pengumpulan_zis_dskl') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.pengumpulan_zis_dskl') || request()->routeIs('pemerintah.dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">📥</span>
      <span class="text-sm font-semibold">Pengumpulan ZIS-DSKL</span>
    </a>

    <a href="{{ route('pemerintah.penyaluran_zis_dskl') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.penyaluran_zis_dskl') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">📤</span>
      <span class="text-sm font-semibold">Penyaluran ZIS-DSKL</span>
    </a>

    <a href="{{ route('pemerintah.program_penyaluran') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('pemerintah.laporan_baznas') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">📊</span>
      <span class="text-sm font-semibold">Laporan BAZNAS</span>
    </a>
    @endif

    {{-- MENU KHUSUS: KEUANGAN --}}
    @if(Auth::user()->role === 'keuangan')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Finance Menu</p>

    <a href="{{ route('keuangan.pengajuan') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('keuangan.pengajuan*') || request()->routeIs('keuangan.review*') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">⚖️</span>
      <span class="text-sm font-semibold">Persetujuan Program</span>
    </a>
    @endif
    {{-- ========================================== --}}
    {{-- MENU KHUSUS: PENYALURAN --}}
    {{-- ========================================== --}}
    @if(Auth::user()->role === 'penyaluran')
    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mt-4 mb-2 tracking-widest">Distribution Menu</p>
    
    <a href="{{ route('penyaluran.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('penyaluran.dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">🏠</span>
      <span class="text-sm font-semibold">Kelola Program</span>
    </a>
    
    <a href="{{ route('penyaluran.konfirmasi') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('penyaluran.konfirmasi') || request()->routeIs('penyaluran.upload.bukti') ? 'bg-emerald-600 text-white shadow-lg' : 'hover:bg-slate-800' }}">
      <span class="text-lg">✅</span>
      <span class="text-sm font-semibold">Konfirmasi Program</span>
    </a>
    @endif

  </nav>

  <div class="p-4 border-t border-slate-800">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-900/20 text-red-400 transition-all text-sm font-bold group">
        <span class="group-hover:scale-110 transition-transform">🔌</span> Disconnect
      </button>
    </form>
  </div>
</aside>