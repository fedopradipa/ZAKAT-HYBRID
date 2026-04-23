{{-- resources/views/dashboard/muzakki/history.blade.php --}}
<x-layouts.portal title="Riwayat Zakat Saya">

  @push('styles')
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
    .history-page { font-family: 'Plus Jakarta Sans', sans-serif; }
    .fade-up { animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
    .fade-up:nth-child(1) { animation-delay: 0.05s; }
    .fade-up:nth-child(2) { animation-delay: 0.10s; }
    .fade-up:nth-child(3) { animation-delay: 0.15s; }
    .fade-up:nth-child(4) { animation-delay: 0.20s; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .status-pill {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 3px 10px; border-radius: 999px;
      font-size: 10px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
    }
  </style>
  @endpush

  <div class="history-page max-w-4xl mx-auto px-4 py-10 space-y-8">

    {{-- ── HEADER ───────────────────────────────────────────────────── --}}
    <div class="fade-up">
      <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest mb-1">
        Dashboard Muzakki
      </p>
      <h1 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Pembayaran ZIS</h1>
      <p class="text-slate-500 text-sm mt-1">
        Semua transaksi tercatat permanen di Blockchain dan tidak dapat dimanipulasi.
      </p>
    </div>

    {{-- ── INFO HAK AMIL ────────────────────────────────────────────── --}}
    <div class="fade-up bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex gap-4 items-start">
      <div class="text-2xl mt-0.5">💡</div>
      <div>
        <p class="text-sm font-bold text-amber-800 mb-0.5">Tentang Potongan Hak Amil</p>
        <p class="text-xs text-amber-700 leading-relaxed">
          Sesuai syariat Islam (QS. At-Taubah: 60), sebesar <strong>12.5%</strong> dari setiap
          pembayaran Anda dialokasikan sebagai <strong>Hak Amil BAZNAS</strong> untuk biaya
          operasional pengelolaan zakat. Sebesar <strong>87.5%</strong> sisanya disalurkan
          langsung kepada mustahik.
        </p>
      </div>
    </div>

    {{-- ── SUMMARY CARDS ────────────────────────────────────────────── --}}
    <div class="fade-up grid grid-cols-1 sm:grid-cols-3 gap-4">

      {{-- Total Dibayarkan --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-base">💳</div>
          <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Dibayarkan</p>
        </div>
        <p class="text-xl font-black text-slate-900 font-mono leading-none">
          {{ number_format($totalSetor, 6) }} <span class="text-xs font-semibold text-slate-400">ETH</span>
        </p>
        <p class="text-[11px] text-slate-400 font-semibold mt-1.5">
          ≈ Rp {{ number_format($totalSetor * ($ethPrice ?? 50000000), 0, ',', '.') }}
        </p>
        <div class="mt-3 pt-3 border-t border-slate-100 flex justify-between text-[10px] text-slate-400 font-semibold">
          <span>Dana ZIS (87.5%)</span>
          <span class="text-emerald-600 font-black">
            {{ number_format($totalSetor * 0.875, 6) }} ETH
          </span>
        </div>
        <div class="flex justify-between text-[10px] text-slate-400 font-semibold mt-1">
          <span>Hak Amil (12.5%)</span>
          <span class="text-amber-500 font-black">
            {{ number_format($totalSetor * 0.125, 6) }} ETH
          </span>
        </div>
      </div>

      {{-- Sudah Tersalurkan --}}
      <div class="bg-white rounded-2xl border border-emerald-200 p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-base">✅</div>
          <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sudah Tersalurkan</p>
        </div>
        <p class="text-xl font-black text-emerald-600 font-mono leading-none">
          {{ number_format($totalDialokasi, 6) }} <span class="text-xs font-semibold text-slate-400">ETH</span>
        </p>
        <p class="text-[11px] text-emerald-600/70 font-semibold mt-1.5">
          ≈ Rp {{ number_format($totalDialokasi * ($ethPrice ?? 50000000), 0, ',', '.') }}
        </p>
        <p class="text-[10px] text-slate-400 font-semibold mt-3 pt-3 border-t border-slate-100">
          Dana yang sudah diterima mustahik
        </p>
      </div>

      {{-- Menunggu Penyaluran --}}
      <div class="bg-white rounded-2xl border border-amber-200 p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
          <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-base">⏳</div>
          <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Menunggu Penyaluran</p>
        </div>
        {{-- Sisa = nominal_bersih - dialokasi, bukan dari totalSetor --}}
        @php
          $totalBersih = $transactions->sum('nominal_bersih');
          $sisaBersih  = $totalBersih - $totalDialokasi;
          $sisaBersih  = max($sisaBersih, 0);
        @endphp
        <p class="text-xl font-black text-amber-600 font-mono leading-none">
          {{ number_format($sisaBersih, 6) }} <span class="text-xs font-semibold text-slate-400">ETH</span>
        </p>
        <p class="text-[11px] text-amber-600/70 font-semibold mt-1.5">
          ≈ Rp {{ number_format($sisaBersih * ($ethPrice ?? 50000000), 0, ',', '.') }}
        </p>
        <p class="text-[10px] text-slate-400 font-semibold mt-3 pt-3 border-t border-slate-100">
          Sedang diproses untuk mustahik berikutnya
        </p>
      </div>

    </div>

    {{-- ── TABEL TRANSAKSI ──────────────────────────────────────────── --}}
    <div class="fade-up bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

      <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
          <h2 class="font-black text-slate-800 text-base">Detail Transaksi</h2>
          <p class="text-[11px] text-slate-400 font-medium mt-0.5">
            Klik hash transaksi untuk melihat bukti di Blockchain
          </p>
        </div>
        <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">
          {{ $transactions->count() }} transaksi
        </span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
              <th class="px-5 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
              <th class="px-5 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis</th>
              <th class="px-5 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Dibayarkan</th>
              <th class="px-5 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Dana ZIS</th>
              <th class="px-5 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Bukti</th>
              <th class="px-5 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($transactions as $tx)
            @php
              $dialokasi = collect($fifoAlokasi)
                ->flatMap(fn($e) => $e['allocations'])
                ->where('transaction.id', $tx->id)
                ->sum('amount');
              $sisa = $tx->nominal_bersih - $dialokasi;
            @endphp
            <tr class="hover:bg-slate-50/60 transition-colors">

              {{-- Tanggal --}}
              <td class="px-5 py-4">
                <p class="text-xs font-bold text-slate-700">{{ $tx->created_at->format('d M Y') }}</p>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $tx->created_at->format('H:i') }} WIB</p>
              </td>

              {{-- Jenis Dana --}}
              <td class="px-5 py-4">
                <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-md text-[10px] font-black uppercase tracking-wide">
                  {{ $tx->jenis_dana }}
                </span>
              </td>

              {{-- Nominal Dibayarkan --}}
              <td class="px-5 py-4 text-right">
                <p class="font-mono font-black text-slate-800 text-sm">
                  {{ number_format($tx->nominal, 6) }}
                  <span class="text-[10px] text-slate-400 font-semibold">ETH</span>
                </p>
                <p class="text-[10px] text-amber-500 font-semibold mt-0.5">
                  -{{ number_format($tx->hak_amil, 6) }} amil
                </p>
              </td>

              {{-- Dana ZIS Bersih --}}
              <td class="px-5 py-4 text-right">
                <p class="font-mono font-black text-emerald-700 text-sm">
                  {{ number_format($tx->nominal_bersih, 6) }}
                  <span class="text-[10px] text-slate-400 font-semibold">ETH</span>
                </p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">87.5% untuk mustahik</p>
              </td>

              {{-- Bukti Blockchain --}}
              <td class="px-5 py-4">
                <a href="https://sepolia.etherscan.io/tx/{{ $tx->tx_hash }}"
                   target="_blank"
                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-mono text-[10px] font-bold hover:underline transition-colors">
                  {{ substr($tx->tx_hash, 0, 10) }}...
                  <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                  </svg>
                </a>
              </td>

              {{-- Status --}}
              <td class="px-5 py-4 text-center">
                @if($sisa <= 0)
                  <span class="status-pill bg-emerald-100 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    Tersalurkan
                  </span>
                @elseif($dialokasi > 0)
                  <span class="status-pill bg-blue-50 text-blue-600 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 inline-block"></span>
                    Sebagian
                  </span>
                @else
                  <span class="status-pill bg-amber-50 text-amber-600 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse inline-block"></span>
                    Diproses
                  </span>
                @endif
              </td>

            </tr>
            @empty
            <tr>
              <td colspan="6" class="px-6 py-16 text-center">
                <div class="text-4xl mb-3 opacity-30">🧾</div>
                <p class="text-slate-500 font-bold text-sm">Belum ada transaksi</p>
                <p class="text-slate-400 text-xs mt-1">Transaksi Anda akan muncul di sini setelah pembayaran berhasil</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>

    {{-- ── KETERANGAN STATUS ────────────────────────────────────────── --}}
    <div class="fade-up bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4">
      <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3">Keterangan Status</p>
      <div class="flex flex-wrap gap-4 text-[11px] font-semibold text-slate-600">
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
          <strong>Tersalurkan</strong> — Dana sudah diterima mustahik
        </span>
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
          <strong>Sebagian</strong> — Dana Anda dipakai di beberapa program
        </span>
        <span class="flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
          <strong>Diproses</strong> — Sedang menunggu program penyaluran berikutnya
        </span>
      </div>
    </div>

  </div>

</x-layouts.portal>