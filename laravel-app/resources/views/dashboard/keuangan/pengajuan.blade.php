<x-layouts.admin title="Antrean Persetujuan Program">

  <div class="mb-8">
    <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Persetujuan Pencairan Dana</h1>
    <p class="text-slate-500 text-sm font-medium">Tinjau rincian program penyaluran sebelum mengeksekusi transaksi ke Blockchain.</p>
  </div>

  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
    <div class="bg-amber-500 px-6 py-4 border-b-2 border-slate-300 flex justify-between items-center">
      <h4 class="text-white font-black uppercase text-[10px] tracking-widest flex items-center gap-2">
        <span>⏳</span> Antrean Pengajuan Masuk (Pending)
      </h4>
      <span class="bg-amber-900/20 text-amber-900 text-[9px] font-black px-3 py-1 rounded-full uppercase">
        {{ $pendingPrograms->count() }} Perlu Review
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b-2 border-slate-200">
            <th class="px-6 py-5">Program & Deskripsi</th>
            <th class="px-6 py-5">Klasifikasi</th>
            <th class="px-6 py-5 text-right">Dana Dibutuhkan</th>
            <th class="px-6 py-5">Tgl Pengajuan</th>
            <th class="px-6 py-5 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-xs font-medium">
          @forelse($pendingPrograms as $program)
          <tr class="border-b border-slate-200 hover:bg-amber-50/30 transition-colors group">
            <td class="px-6 py-4 max-w-xs">
              <p class="font-black text-slate-900 text-sm mb-1 group-hover:text-amber-600 transition-colors">{{ $program->judul }}</p>
              <p class="text-[10px] text-slate-400 line-clamp-2 italic leading-relaxed">
                {{ $program->deskripsi }}
              </p>
            </td>

            <td class="px-6 py-4">
              <div class="space-y-1">
                <span class="block font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded text-[9px] w-fit uppercase">
                  📁 {{ $program->bidang }}
                </span>
                <span class="block text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                  👤 Asnaf: {{ $program->asnaf }}
                </span>
              </div>
            </td>

            <td class="px-6 py-4 text-right">
              <div class="inline-block text-right">
                <p class="font-mono font-black text-amber-600 text-sm bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl">
                  {{ number_format($program->dana_dibutuhkan, 4) }} <span class="text-[10px]">ETH</span>
                </p>
                <p class="text-[9px] text-slate-400 mt-1 font-bold italic">Bentuk: {{ $program->bentuk_bantuan }}</p>
              </div>
            </td>

            <td class="px-6 py-4">
              <p class="text-slate-600 font-bold">{{ $program->created_at->format('d M Y') }}</p>
              <p class="text-[10px] text-slate-400">{{ $program->created_at->format('H:i') }} WIB</p>
            </td>

            <td class="px-6 py-4 text-center">
              <a href="{{ route('keuangan.review', $program->id) }}" class="bg-slate-800 hover:bg-black text-white font-black py-3 px-6 rounded-xl transition-all shadow-md active:scale-95 text-[10px] uppercase tracking-widest inline-block">
                Tinjau & Cairkan
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-6 py-24 text-center">
              <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mb-4 border-2 border-dashed border-slate-200">☕</div>
                <h5 class="text-slate-800 font-black uppercase text-xs tracking-widest">Semua Beres!</h5>
                <p class="text-slate-400 text-[10px] mt-1 font-medium">Tidak ada antrean pengajuan program saat ini.</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-6 flex items-center gap-4 bg-slate-100 p-4 rounded-xl border border-slate-200">
    <span class="text-xl">ℹ️</span>
    <p class="text-[10px] text-slate-500 font-medium leading-relaxed">
      <strong class="text-slate-700 uppercase">Catatan Keuangan:</strong> <br>
      Pastikan saldo dompet MetaMask Anda mencukupi untuk membayar <em class="font-bold">Gas Fee</em> saat menekan tombol Cairkan. Transaksi yang sudah masuk ke Blockchain tidak dapat dibatalkan.
    </p>
  </div>

</x-layouts.admin>