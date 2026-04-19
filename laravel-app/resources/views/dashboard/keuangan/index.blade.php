<x-layouts.admin title="Overview Keuangan">
  @if($pendingCount > 0)
  <div class="mb-8 bg-amber-50 border-2 border-amber-200 p-4 rounded-2xl flex items-center justify-between shadow-sm animate-pulse">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg">🔔</div>
      <div>
        <h4 class="text-amber-900 font-black text-sm uppercase">Ada {{ $pendingCount }} Pengajuan Program Baru!</h4>
        <p class="text-amber-700 text-xs">Tim Penyaluran membutuhkan persetujuan pencairan dana Anda.</p>
      </div>
    </div>
    <a href="{{ route('keuangan.pengajuan') }}" class="bg-amber-900 text-white px-6 py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all">
      Tinjau Sekarang
    </a>
  </div>
  @endif
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden group">
      <div class="relative z-10">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Saldo (Blockchain)</p>
        <h3 class="text-3xl font-black text-slate-900">{{ number_format($totalEth, 4) }} <span class="text-sm text-emerald-500">ETH</span></h3>
        <p class="text-[10px] text-slate-400 mt-2">≈ Rp {{ number_format($totalIdr, 0, ',', '.') }}</p>
      </div>
      <div class="absolute -right-4 -bottom-4 text-slate-50 text-8xl transition-transform group-hover:scale-110">⛓️</div>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden group">
      <div class="relative z-10">
        <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Donatur (Muzakki)</p>
        <h3 class="text-3xl font-black text-slate-900">{{ $totalTransaksi }} <span class="text-sm text-blue-500">Jiwa</span></h3>
        <p class="text-[10px] text-emerald-500 mt-2">▲ 12% dibanding bulan lalu</p>
      </div>
      <div class="absolute -right-4 -bottom-4 text-slate-50 text-8xl transition-transform group-hover:scale-110">👥</div>
    </div>

    <div class="bg-emerald-900 p-6 rounded-3xl shadow-xl shadow-emerald-900/20 relative overflow-hidden">
      <div class="relative z-10 text-white">
        <p class="text-emerald-300/60 text-xs font-bold uppercase tracking-wider mb-1">Node Status</p>
        <h3 class="text-xl font-bold">Polygon Mainnet</h3>
        <div class="flex items-center gap-2 mt-4">
          <span class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
          <span class="text-[10px] font-mono text-emerald-300">Synchronized & Secured</span>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
      <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest">Transaksi Terbaru</h4>
      <button class="text-emerald-600 text-xs font-bold hover:underline">Lihat Semua</button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-[10px] font-black text-slate-400 uppercase tracking-tighter bg-slate-50/50">
            <th class="px-6 py-4">Muzakki</th>
            <th class="px-6 py-4">Tipe</th>
            <th class="px-6 py-4">Nominal</th>
            <th class="px-6 py-4">Hash Blockchain</th>
            <th class="px-6 py-4">Waktu</th>
          </tr>
        </thead>
        <tbody class="text-xs font-medium">
          @foreach($recentTransactions as $trx)
          <tr class="border-t border-slate-50 hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4">
              <p class="font-bold text-slate-800">{{ $trx->user->name ?? 'Anonim' }}</p>
              <p class="text-[10px] text-slate-400 font-mono">{{ substr($trx->user->wallet_address, 0, 10) }}...</p>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 rounded bg-slate-100 text-slate-600 font-bold uppercase text-[9px]">
                {{ $trx->jenis_dana }}
              </span>
            </td>
            <td class="px-6 py-4 font-mono font-bold text-emerald-600">
              {{ $trx->nominal }} ETH
            </td>
            <td class="px-6 py-4">
              <a href="https://polygonscan.com/tx/{{ $trx->tx_hash }}" target="_blank" class="text-blue-500 hover:underline font-mono">
                {{ substr($trx->tx_hash, 0, 15) }}...
              </a>
            </td>
            <td class="px-6 py-4 text-slate-400">
              {{ $trx->created_at->diffForHumans() }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-layouts.admin>