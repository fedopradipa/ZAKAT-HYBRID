<nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
  <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

    <div class="flex items-center gap-8">
      <a href="/" class="flex items-center gap-2">
        <div class="text-xl">🦅</div>
        <span class="text-emerald-700 font-extrabold text-sm tracking-tighter mt-1">BAZNAS</span>
      </a>
      <div class="hidden md:flex items-center gap-6 text-slate-500 font-semibold text-xs">
        <a href="/" class="hover:text-emerald-600 transition-colors">Home</a>
        <a href="#" class="hover:text-emerald-600 transition-colors">Program</a>
        <a href="#" class="hover:text-emerald-600 transition-colors">Informasi</a>
      </div>
    </div>

    <div class="flex items-center gap-4 relative">

      {{-- 1. Tombol Bayar Zakat (Muncul hanya jika bukan Admin) --}}
      @if(!Auth::check() || Auth::user()->role === 'muzakki')
      @if(!request()->routeIs('zakat.form'))
      <a href="{{ route('zakat.form') }}" class="bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-bold py-1.5 px-4 rounded-lg text-xs flex items-center gap-2 shadow-sm transition-all active:scale-95">
        <span>💰</span> Bayar Zakat
      </a>
      @endif
      @endif

      {{-- 2. Area User / Login --}}
      @auth
      {{-- Dropdown Profile --}}
      <div class="relative group z-50">
        <button class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors bg-white shadow-sm">
          <span class="bg-slate-500 text-white text-[10px] px-2 py-0.5 rounded font-bold capitalize">{{ Auth::user()->role }}</span>
          <span class="text-xs font-mono font-bold text-slate-700">{{ substr(Auth::user()->wallet_address, 0, 6) }}...{{ substr(Auth::user()->wallet_address, -4) }}</span>
          <svg class="w-3 h-3 text-slate-400 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <div class="absolute right-0 mt-1 w-64 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all origin-top-right transform scale-95 group-hover:scale-100">
          <div class="p-4 bg-slate-50/50 rounded-t-xl border-b border-slate-100">
            <p class="text-[10px] text-slate-400 font-medium mb-1">Wallet terhubung</p>
            <p class="text-xs font-mono font-bold text-slate-800 break-all mb-2">
              {{ Auth::user()->wallet_address }}
            </p>
            <span class="bg-slate-500 text-white text-[10px] px-2 py-0.5 rounded font-bold capitalize">{{ Auth::user()->role }}</span>
          </div>
          <div class="p-2">
            {{-- Arahkan ke dashboard sesuai role --}}
            <a href="{{ route(Auth::user()->role . '.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-600 font-semibold text-xs transition-colors">
              <span>📜</span> Dashboard
            </a>
            <hr class="my-1 border-slate-100">
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-red-50 text-red-500 font-semibold text-xs transition-colors text-left font-bold">
                <span>🔌</span> Disconnect
              </button>
            </form>
          </div>
        </div>
      </div>
      @else
      {{-- Tombol Login jika belum Auth --}}
      <button id="btnConnect" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-4 rounded-lg text-xs transition-all shadow-sm active:scale-95">
        Login Web3
      </button>
      @endauth

    </div>
  </div>
</nav>