<header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center z-10 shadow-sm">
  <h1 class="text-xl font-bold text-slate-800">{{ $title ?? 'Dashboard' }}</h1>

  <div class="flex items-center gap-4">
    <div class="text-right hidden sm:block">
      <p class="text-xs font-bold text-slate-900 leading-none">{{ Auth::user()->name }}</p>
      <p class="text-[10px] text-slate-400 font-medium uppercase mt-1">{{ Auth::user()->role }}</p>
    </div>
    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-bold border border-emerald-200">
      {{ substr(Auth::user()->name, 0, 1) }}
    </div>
  </div>
</header>