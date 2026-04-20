{{-- resources/views/dashboard/muzakki/history.blade.php --}}

<x-layouts.portal title="Riwayat Zakat Saya">
  <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">

    {{-- Header --}}
    <div>
      <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Riwayat Setoran Zakat</h1>
      <p class="text-slate-500 text-sm font-semibold mt-1">
        Pantau riwayat pembayaran zakat Anda. Untuk melihat rincian penyaluran, silakan gunakan menu Lacak Penyaluran.
      </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm">
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Disetor</p>
        <p class="text-xl font-black text-slate-900 font-mono">{{ number_format($totalSetor, 4) }} <span class="text-xs text-slate-400">ETH</span></p>
      </div>
      <div class="bg-white rounded-2xl border-2 border-emerald-200 p-5 shadow-sm">
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Sudah Disalurkan</p>
        <p class="text-xl font-black text-emerald-600 font-mono">{{ number_format($totalDialokasi, 4) }} <span class="text-xs text-slate-400">ETH</span></p>
      </div>
      <div class="bg-white rounded-2xl border-2 border-amber-200 p-5 shadow-sm">
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Dalam Antrian</p>
        <p class="text-xl font-black text-amber-600 font-mono">{{ number_format($sisaBelumCair, 4) }} <span class="text-xs text-slate-400">ETH</span></p>
      </div>
    </div>

    {{-- Riwayat Setoran --}}
    <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
      <div class="bg-slate-800 px-6 py-4">
        <h3 class="text-white font-black uppercase text-xs tracking-widest">🧾 Riwayat Transaksi</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-slate-50 text-[10px] font-black text-slate-600 uppercase tracking-widest border-b-2 border-slate-200">
            <tr>
              <th class="px-5 py-4">Tanggal</th>
              <th class="px-5 py-4">Jenis Dana</th>
              <th class="px-5 py-4 text-right">Nominal</th>
              <th class="px-5 py-4">Tx Hash</th>
              <th class="px-5 py-4 text-center">Status FIFO</th>
            </tr>
          </thead>
          <tbody class="divide-y-2 divide-slate-100">
            @forelse($transactions as $tx)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-5 py-4 text-xs font-bold text-slate-700">
                {{ $tx->created_at->format('d/m/Y H:i') }}
              </td>
              <td class="px-5 py-4">
                <span class="bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-black px-2 py-1 rounded-full uppercase">
                  {{ $tx->jenis_dana }}
                </span>
              </td>
              <td class="px-5 py-4 text-right font-mono font-black text-slate-900 text-sm">
                {{ number_format($tx->nominal, 4) }} ETH
              </td>
              <td class="px-5 py-4">
                <a href="https://sepolia.etherscan.io/tx/{{ $tx->tx_hash }}"
                  target="_blank"
                  class="text-blue-600 hover:underline font-mono text-[10px] font-bold">
                  {{ substr($tx->tx_hash, 0, 14) }}...
                </a>
              </td>
              <td class="px-5 py-4 text-center">
                @php
                  $dialokasi = collect($fifoAlokasi)->flatMap(fn($e) => $e['allocations'])
                    ->where('transaction.id', $tx->id)->sum('amount');
                  $sisa = $tx->nominal - $dialokasi;
                @endphp
                @if($sisa <= 0)
                  <span class="bg-emerald-100 text-emerald-700 border-2 border-emerald-300 text-[9px] font-black px-2 py-1 rounded-full uppercase">
                    ✅ Tersalurkan
                  </span>
                @elseif($dialokasi > 0)
                  <span class="bg-amber-100 text-amber-700 border-2 border-amber-300 text-[9px] font-black px-2 py-1 rounded-full uppercase">
                    ⏳ Sebagian
                  </span>
                @else
                  <span class="bg-slate-100 text-slate-600 border-2 border-slate-300 text-[9px] font-black px-2 py-1 rounded-full uppercase">
                    🕐 Antrian
                  </span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="px-6 py-16 text-center">
                <p class="text-slate-400 font-black uppercase text-xs tracking-widest">Belum ada riwayat setoran</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</x-layouts.portal>