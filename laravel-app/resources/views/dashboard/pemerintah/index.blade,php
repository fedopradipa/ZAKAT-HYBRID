{{-- resources/views/dashboard/pemerintah/index.blade.php --}}

<x-layouts.admin title="Monitor Pengawasan Zakat">
  <div class="space-y-8 animate-fade-in-up">
    
    {{-- Header Pengawasan --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-3">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
          </span>
          Mode Auditor
        </div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Panel Pengawasan Nasional</h1>
        <p class="text-slate-500 text-sm font-semibold mt-1">
          Pemantauan real-time arus kas zakat dan kepatuhan penyaluran berbasis Blockchain.
        </p>
      </div>
      <div class="flex gap-2">
        <button onclick="window.print()" class="bg-white text-slate-700 border-2 border-slate-200 font-black py-2.5 px-5 rounded-xl text-xs uppercase tracking-widest hover:bg-slate-50 transition-all active:scale-95 flex items-center gap-2 shadow-sm">
          <span>🖨️</span> Cetak Laporan
        </button>
      </div>
    </div>

    {{-- Makro Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 text-6xl opacity-5">💰</div>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 relative z-10">Total Pengumpulan</p>
        <p class="text-2xl font-black text-slate-900 font-mono relative z-10">1,240.50 <span class="text-xs text-slate-400">ETH</span></p>
        <p class="text-[10px] text-emerald-600 font-bold mt-2 relative z-10 flex items-center gap-1">
          <span>↑</span> 12% dari kuartal lalu
        </p>
      </div>
      
      <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 text-6xl opacity-5">📈</div>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 relative z-10">Efektivitas Penyaluran</p>
        <p class="text-2xl font-black text-emerald-600 font-mono relative z-10">94.2%</p>
        <p class="text-[10px] text-slate-400 font-bold mt-2 relative z-10">Target Nasional: Min. 90%</p>
      </div>
      
      <div class="bg-white rounded-2xl border-2 border-slate-200 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 text-6xl opacity-5">👥</div>
        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 relative z-10">Penerima Manfaat</p>
        <p class="text-2xl font-black text-blue-600 font-mono relative z-10">8,420 <span class="text-xs text-slate-400 font-sans">Jiwa</span></p>
        <p class="text-[10px] text-slate-400 font-bold mt-2 relative z-10">Telah diverifikasi</p>
      </div>
      
      <div class="bg-white rounded-2xl border-2 border-amber-200 p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-4 -top-4 text-6xl opacity-10">⚖️</div>
        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-1 relative z-10">Rasio Hak Amil</p>
        <p class="text-2xl font-black text-amber-600 font-mono relative z-10">11.5%</p>
        <p class="text-[10px] text-amber-600/80 font-bold mt-2 relative z-10">Batas Maksimal Regulasi: 12.5%</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      {{-- Kolom Kiri: Sebaran Program (Memakan 2 Kolom) --}}
      <div class="lg:col-span-2 space-y-8">
        
        {{-- Card: Sebaran Penyaluran --}}
        <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
          <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
              <h3 class="text-white font-black uppercase text-xs tracking-widest">📈 Sebaran Penyaluran per Asnaf/Bidang</h3>
          </div>
          <div class="p-6">
              <div class="space-y-6">
                  @php
                      // Data Dummy Agregat untuk Pemerintah
                      $sebaran = [
                          ['label' => 'Pendidikan (Beasiswa & Fasilitas)', 'color' => 'bg-blue-500', 'percent' => 40, 'amount' => '496.20'],
                          ['label' => 'Kesehatan & Medis', 'color' => 'bg-emerald-500', 'percent' => 25, 'amount' => '310.12'],
                          ['label' => 'Kemanusiaan & Bencana', 'color' => 'bg-red-500', 'percent' => 20, 'amount' => '248.10'],
                          ['label' => 'Ekonomi Mikro & Dakwah', 'color' => 'bg-amber-500', 'percent' => 15, 'amount' => '186.08'],
                      ];
                  @endphp

                  @foreach($sebaran as $item)
                  <div>
                      <div class="flex justify-between text-xs font-bold text-slate-700 mb-2 uppercase tracking-wide">
                          <span>{{ $item['label'] }}</span>
                          <div class="text-right">
                              <span class="text-slate-900 font-black font-mono">{{ $item['amount'] }} ETH</span>
                              <span class="text-[10px] text-slate-400 ml-1">({{ $item['percent'] }}%)</span>
                          </div>
                      </div>
                      <div class="w-full bg-slate-100 rounded-full h-3">
                          <div class="{{ $item['color'] }} h-3 rounded-full shadow-inner" style="width: {{ $item['percent'] }}%"></div>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
        </div>

        {{-- Card: Log Audit Transparansi --}}
        <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
          <div class="bg-slate-50 px-6 py-4 border-b-2 border-slate-200">
            <h3 class="text-slate-800 font-black uppercase text-xs tracking-widest">📋 Log Audit Program Terselesaikan</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-100/50 text-[10px] font-black text-slate-500 uppercase tracking-widest border-b-2 border-slate-200">
                <tr>
                  <th class="px-5 py-3">Program</th>
                  <th class="px-5 py-3">Anggaran</th>
                  <th class="px-5 py-3 text-center">Status IPFS</th>
                  <th class="px-5 py-3 text-center">Smart Contract</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-5 py-4">
                    <p class="text-xs font-black text-slate-800">Bantuan Modal UMKM Tasikmalaya</p>
                    <p class="text-[10px] text-slate-400 font-bold mt-0.5">Selesai: 18 April 2026</p>
                  </td>
                  <td class="px-5 py-4 text-xs font-mono font-black text-slate-700">12.5000 ETH</td>
                  <td class="px-5 py-4 text-center">
                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 border border-emerald-300 text-[9px] font-black px-2 py-1 rounded-full uppercase">
                      ✅ Terenkripsi
                    </span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <a href="#" class="text-blue-600 hover:underline font-mono text-[10px] font-bold">0xabc...123</a>
                  </td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-5 py-4">
                    <p class="text-xs font-black text-slate-800">Beasiswa Santri Berprestasi</p>
                    <p class="text-[10px] text-slate-400 font-bold mt-0.5">Selesai: 15 April 2026</p>
                  </td>
                  <td class="px-5 py-4 text-xs font-mono font-black text-slate-700">8.2000 ETH</td>
                  <td class="px-5 py-4 text-center">
                    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 border border-emerald-300 text-[9px] font-black px-2 py-1 rounded-full uppercase">
                      ✅ Terenkripsi
                    </span>
                  </td>
                  <td class="px-5 py-4 text-center">
                    <a href="#" class="text-blue-600 hover:underline font-mono text-[10px] font-bold">0xdef...456</a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-center">
            <a href="#" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Lihat Seluruh Log Audit &rarr;</a>
          </div>
        </div>

      </div>

      {{-- Kolom Kanan: Status Kepatuhan (Memakan 1 Kolom) --}}
      <div class="space-y-6">
        
        {{-- Integritas Sistem --}}
        <div class="bg-violet-900 rounded-2xl border-2 border-violet-700 p-6 shadow-md text-white relative overflow-hidden">
          <div class="absolute -right-8 -top-8 text-8xl opacity-10">🧊</div>
          <h3 class="font-black uppercase text-xs tracking-widest mb-4 flex items-center gap-2 relative z-10">
            <span>🛡️</span> Indeks Integritas Sistem
          </h3>
          
          <div class="space-y-4 relative z-10">
            <div class="bg-violet-800/50 p-4 rounded-xl border border-violet-600/50">
              <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] text-violet-200 font-bold uppercase tracking-wider">Pencatatan Blockchain</span>
                <span class="text-xs font-black text-emerald-400">100%</span>
              </div>
              <p class="text-[9px] text-violet-300">Seluruh transaksi sukses tercatat di jaringan Ethereum (Sepolia).</p>
            </div>
            
            <div class="bg-violet-800/50 p-4 rounded-xl border border-violet-600/50">
              <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] text-violet-200 font-bold uppercase tracking-wider">Kepatuhan IPFS (Bukti)</span>
                <span class="text-xs font-black text-amber-400">98.5%</span>
              </div>
              <p class="text-[9px] text-violet-300">Terdapat 1.5% program dalam masa tunggu unggah dokumentasi.</p>
            </div>

            <div class="bg-violet-800/50 p-4 rounded-xl border border-violet-600/50">
              <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] text-violet-200 font-bold uppercase tracking-wider">Status Kontrak Pintar</span>
                <span class="text-xs font-black text-emerald-400">Aman</span>
              </div>
              <p class="text-[9px] text-violet-300">Tidak terdeteksi anomali penarikan dana di luar algoritma FIFO.</p>
            </div>
          </div>
        </div>

        {{-- Ringkasan Audit Cepat --}}
        <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 shadow-sm">
          <h3 class="text-slate-800 font-black uppercase text-xs tracking-widest mb-4">Aksi Rekomendasi</h3>
          <ul class="space-y-3">
            <li class="flex gap-3 text-sm">
              <span class="text-emerald-500">✓</span>
              <span class="text-slate-600 font-medium text-xs">Tata kelola bulan ini sesuai standar operasional BAZNAS.</span>
            </li>
            <li class="flex gap-3 text-sm">
              <span class="text-amber-500">!</span>
              <span class="text-slate-600 font-medium text-xs">Tinjau 2 program penyaluran yang telat mengunggah bukti IPFS lebih dari 7 hari.</span>
            </li>
            <li class="flex gap-3 text-sm">
              <span class="text-blue-500">ℹ</span>
              <span class="text-slate-600 font-medium text-xs">Pencetakan laporan audit kuartal pertama sudah dapat dilakukan.</span>
            </li>
          </ul>
        </div>

      </div>

    </div>
  </div>

  @push('styles')
  <style>
      .animate-fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(15px); }
      @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
      @media print {
        body * { visibility: hidden; }
        .animate-fade-in-up, .animate-fade-in-up * { visibility: visible; }
        .animate-fade-in-up { position: absolute; left: 0; top: 0; width: 100%; animation: none; transform: none; opacity: 1;}
        button { display: none !important; }
      }
  </style>
  @endpush
</x-layouts.admin>