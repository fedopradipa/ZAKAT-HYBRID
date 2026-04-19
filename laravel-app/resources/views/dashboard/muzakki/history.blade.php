{{-- resources/views/dashboard/muzakki/history.blade.php --}}

<x-layouts.portal title="Riwayat Zakat Saya">
  <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">

    {{-- Header --}}
    <div>
      <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Riwayat & Pelacakan Dana</h1>
      <p class="text-slate-500 text-sm font-semibold mt-1">
        Lacak ke mana dana zakat Anda disalurkan secara transparan.
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

    {{-- FIFO Alokasi --}}
    @if(!empty($fifoAlokasi))
    <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
      <div class="bg-[#5c8a06] px-6 py-4">
        <h3 class="text-white font-black uppercase text-xs tracking-widest">📊 Rincian Alokasi Dana Anda</h3>
      </div>
      <div class="divide-y-2 divide-slate-100">
        @foreach($fifoAlokasi as $entry)
        <div class="p-6">
          <div class="flex items-start justify-between mb-3">
            <div>
              <p class="text-sm font-black text-slate-900">{{ $entry['program']->judul }}</p>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5 uppercase tracking-widest">
                {{ $entry['program']->bidang }} · {{ $entry['program']->tanggal_pelaksanaan->format('d/m/Y') }}
              </p>
            </div>
            <span class="bg-emerald-100 text-emerald-800 border-2 border-emerald-300 text-[10px] font-black px-3 py-1 rounded-full uppercase">
              {{ number_format($entry['total_kontribusi'], 4) }} ETH
            </span>
          </div>

          {{-- Progress bar kontribusi --}}
          @php
            $persen = $entry['program']->dana_dibutuhkan > 0
              ? ($entry['total_kontribusi'] / $entry['program']->dana_dibutuhkan) * 100
              : 0;
          @endphp
          <div class="mt-3">
            <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
              <span>Kontribusi Anda</span>
              <span>{{ number_format($persen, 1) }}% dari total program</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <div class="bg-emerald-500 h-2 rounded-full transition-all"
                style="width: {{ min($persen, 100) }}%"></div>
            </div>
          </div>

          {{-- Detail per transaksi --}}
          @foreach($entry['allocations'] as $alloc)
          <div class="mt-3 bg-slate-50 rounded-xl p-3 flex items-center justify-between border border-slate-200">
            <div>
              <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Dari Setoran</p>
              <p class="text-xs font-mono font-bold text-slate-800 mt-0.5">
                {{ substr($alloc['transaction']->tx_hash, 0, 18) }}...
              </p>
              <p class="text-[10px] text-slate-400 mt-0.5">
                {{ $alloc['transaction']->created_at->format('d/m/Y H:i') }} ·
                {{ $alloc['transaction']->jenis_dana }}
              </p>
            </div>
            <span class="font-mono font-black text-emerald-700 text-sm">
              {{ number_format($alloc['amount'], 4) }} ETH
            </span>
          </div>
          @endforeach
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Riwayat Setoran --}}
    <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
      <div class="bg-slate-800 px-6 py-4">
        <h3 class="text-white font-black uppercase text-xs tracking-widest">🧾 Riwayat Setoran</h3>
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