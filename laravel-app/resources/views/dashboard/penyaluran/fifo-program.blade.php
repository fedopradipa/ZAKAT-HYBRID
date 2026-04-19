{{-- resources/views/dashboard/penyaluran/fifo-program.blade.php --}}

<x-layouts.admin title="Sumber Dana Program">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Sumber Dana Program</h1>
      <p class="text-slate-500 text-xs font-semibold mt-1">Rincian donatur yang mendanai program ini via FIFO.</p>
    </div>
    <a href="{{ route('penyaluran.konfirmasi') }}"
      class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase transition-all flex items-center gap-2">
      ← Kembali
    </a>
  </div>

  {{-- Info Program --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden mb-6">
    <div class="bg-[#5c8a06] px-6 py-4">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">📋 Detail Program</h3>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Judul</label>
        <p class="text-sm font-black text-slate-900">{{ $program->judul }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bidang</label>
        <p class="text-sm font-bold text-slate-700">{{ $program->bidang }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Dana</label>
        <p class="text-sm font-black text-emerald-600 font-mono">{{ number_format($program->dana_dibutuhkan, 4) }} ETH</p>
      </div>
    </div>
  </div>

  {{-- Tabel Sumber Dana --}}
  @if(!empty($detail) && !empty($detail['allocations']))
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
    <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">👥 Donatur via FIFO</h3>
      <span class="bg-emerald-500 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase">
        {{ count($detail['allocations']) }} Donatur
      </span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-[10px] font-black text-slate-600 uppercase tracking-widest border-b-2 border-slate-200">
          <tr>
            <th class="px-5 py-4">No</th>
            <th class="px-5 py-4">Wallet Muzakki</th>
            <th class="px-5 py-4">Waktu Setor</th>
            <th class="px-5 py-4">Jenis Dana</th>
            <th class="px-5 py-4 text-right">Kontribusi</th>
            <th class="px-5 py-4 text-center">Porsi</th>
          </tr>
        </thead>
        <tbody class="divide-y-2 divide-slate-100">
          @foreach($detail['allocations'] as $i => $alloc)
          @php
            $porsi = $program->dana_dibutuhkan > 0
              ? ($alloc['amount'] / $program->dana_dibutuhkan) * 100
              : 0;
          @endphp
          <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50' }} hover:bg-emerald-50 transition-colors">
            <td class="px-5 py-4 text-xs font-black text-slate-500">{{ $i + 1 }}</td>
            <td class="px-5 py-4 font-mono text-xs font-bold text-slate-800">
              {{ $alloc['user']?->wallet_address ?? '-' }}
            </td>
            <td class="px-5 py-4 text-xs font-bold text-slate-600">
              {{ $alloc['transaction']->created_at->format('d/m/Y H:i') }}
            </td>
            <td class="px-5 py-4">
              <span class="bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-black px-2 py-1 rounded-full uppercase">
                {{ $alloc['transaction']->jenis_dana }}
              </span>
            </td>
            <td class="px-5 py-4 text-right font-mono font-black text-emerald-700 text-sm">
              {{ number_format($alloc['amount'], 4) }} ETH
            </td>
            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-2">
                  <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min($porsi, 100) }}%"></div>
                </div>
                <span class="text-[10px] font-black text-slate-600 w-10 text-right">{{ number_format($porsi, 1) }}%</span>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @else
  <div class="bg-white rounded-2xl border-2 border-slate-200 p-16 text-center">
    <p class="text-4xl mb-3">📭</p>
    <p class="text-slate-400 font-black uppercase text-xs tracking-widest">Data FIFO belum tersedia</p>
  </div>
  @endif

</x-layouts.admin>