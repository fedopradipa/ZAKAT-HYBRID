{{-- resources/views/dashboard/pemerintah/pengumpulanZIS/index.blade.php --}}

<x-layouts.admin title="Pengumpulan ZIS DSKL">

  @push('styles')
  <style>
    .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
  @endpush

  <div class="space-y-6 animate-fade-in-up">

    {{-- ── HEADER ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <h1 class="text-xl font-black text-slate-800 tracking-tight">PENGUMPULAN ZIS DSKL</h1>

      <form method="GET" action="{{ route('pemerintah.pengumpulan_zis_dskl') }}" class="flex items-center gap-2">
        <label class="text-xs font-semibold text-slate-500">Pilih Tahun Pengumpulan</label>
        <input
          type="number"
          name="tahun"
          value="{{ $tahun }}"
          min="2020"
          max="{{ now()->year }}"
          placeholder="Tahun"
          class="border border-slate-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:border-emerald-500 w-28"
        />
        <button type="submit"
          class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2 rounded-lg text-sm transition-all active:scale-95">
          Filter
        </button>
      </form>
    </div>

    {{-- ── SUB HEADER TAHUN ─────────────────────────────────────────── --}}
    <div class="flex justify-between items-end">
        <p class="text-sm font-semibold text-slate-700">
        Informasi Pengumpulan Pada Tahun {{ $tahun }}
        </p>
        <p class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-md border border-slate-200">
            Kurs ETH: Rp {{ number_format($ethPriceIdr, 0, ',', '.') }}
        </p>
    </div>

    {{-- ── 4 CARDS ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

      {{-- Card: Zakat --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-emerald-300 transition-colors">
        <p class="text-xs text-slate-500 font-semibold mb-2">Zakat</p>
        <p class="text-2xl font-black text-slate-800">
          Rp {{ number_format($totalZakat * $ethPriceIdr, 0, ',', '.') }}
        </p>
        <p class="text-xs font-bold text-emerald-600 mt-1">
          ≈ {{ number_format($totalZakat, 4, ',', '.') }} ETH
        </p>
      </div>

      {{-- Card: Infak / Sedekah --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-blue-300 transition-colors">
        <p class="text-xs text-slate-500 font-semibold mb-2">Infak / Sedekah</p>
        <p class="text-2xl font-black text-slate-800">
          Rp {{ number_format(($totalInfak ?? 0) * $ethPriceIdr, 0, ',', '.') }}
        </p>
        <p class="text-xs font-bold text-blue-600 mt-1">
          ≈ {{ number_format($totalInfak ?? 0, 4, ',', '.') }} ETH
        </p>
      </div>

      {{-- Card: DSKL --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-blue-300 transition-colors">
        <p class="text-xs text-slate-500 font-semibold mb-2">DSKL</p>
        <p class="text-2xl font-black text-slate-800">
          Rp {{ number_format(($totalDskl ?? 0) * $ethPriceIdr, 0, ',', '.') }}
        </p>
        <p class="text-xs font-bold text-blue-600 mt-1">
          ≈ {{ number_format($totalDskl ?? 0, 4, ',', '.') }} ETH
        </p>
      </div>

      {{-- Card: Hak Amil --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-amber-300 transition-colors">
        <p class="text-xs text-slate-500 font-semibold mb-2">Hak Amil</p>
        <p class="text-2xl font-black text-slate-800">
          Rp {{ number_format(($totalHakAmil ?? 0) * $ethPriceIdr, 0, ',', '.') }}
        </p>
        <p class="text-xs font-bold text-amber-600 mt-1">
          ≈ {{ number_format($totalHakAmil ?? 0, 4, ',', '.') }} ETH
        </p>
      </div>

    </div>

    {{-- ── ROW 1 CHARTS: Bar ZIS DSKL + Donut ─────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- Bar Chart: Pengumpulan Bulanan ZIS DSKL --}}
      <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-black text-slate-800 text-base mb-1">Pengumpulan Bulanan ZIS DSKL</h3>
        <div class="flex items-center gap-2 mb-4">
          <span class="inline-block w-3 h-3 rounded-sm bg-emerald-500"></span>
          <span class="text-xs text-slate-500 font-semibold">Pengumpulan (IDR Estimasi)</span>
        </div>
        <div class="relative h-64 w-full">
          <canvas id="barChart"></canvas>
        </div>
      </div>

      {{-- Donut Chart: Persentase Jenis Dana --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
        <h3 class="font-black text-slate-800 text-base mb-1 text-center">Persentase Jenis Dana</h3>
        <div class="relative flex-1 flex justify-center items-center min-h-[220px]">
          <canvas id="donutChart"></canvas>
        </div>
      </div>

    </div>

    {{-- ── ROW 2 CHARTS: Line Chart Zakat vs Infak ─────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <h3 class="font-black text-slate-800 text-base mb-1">Tren Zakat vs Infak/DSKL</h3>
      <div class="flex items-center gap-4 mb-4">
        <span class="flex items-center gap-1.5">
          <span class="inline-block w-3 h-3 rounded-sm bg-orange-400"></span>
          <span class="text-xs text-slate-500 font-semibold">Zakat</span>
        </span>
        <span class="flex items-center gap-1.5">
          <span class="inline-block w-3 h-3 rounded-sm bg-blue-400"></span>
          <span class="text-xs text-slate-500 font-semibold">Infak / Lainnya</span>
        </span>
      </div>
      <div class="relative h-64 w-full">
        <canvas id="lineChart"></canvas>
      </div>
    </div>

    {{-- ── TABEL DETAIL SETORAN ─────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

      <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
        <h2 class="text-base font-black text-slate-800">Detail Setoran Muzakki</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Seluruh data divalidasi dan tercatat permanen di jaringan Blockchain.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
              <th class="px-6 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">#</th>
              <th class="px-6 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Muzakki</th>
              <th class="px-6 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Jenis Dana</th>
              <th class="px-6 py-3 text-right text-[10px] font-black text-slate-500 uppercase tracking-widest">Nominal</th>
              <th class="px-6 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-widest">Tanggal</th>
              <th class="px-6 py-3 text-center text-[10px] font-black text-slate-500 uppercase tracking-widest">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($dataMuzakki as $tx)
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 text-xs text-slate-400 font-mono">
                  {{ $dataMuzakki->firstItem() + $loop->index }}
                </td>
                <td class="px-6 py-4">
  <p class="font-bold text-slate-800 text-xs capitalize">
    {{ ucfirst($tx->user->role ?? 'muzakki') }}
  </p>
  <p class="font-mono text-[10px] text-slate-400 mt-0.5">
    {{ substr($tx->user->wallet_address ?? '0x000000000000', 0, 6) }}...{{ substr($tx->user->wallet_address ?? '0000', -4) }}
  </p>
</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $tx->jenis_dana }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="font-black text-emerald-600 text-sm">
                    Rp {{ number_format($tx->nominal * $ethPriceIdr, 0, ',', '.') }}
                  </div>
                  <div class="text-[10px] text-slate-500 font-bold mt-0.5">
                    ≈ {{ number_format($tx->nominal, 4, ',', '.') }} ETH
                  </div>
                </td>
                <td class="px-6 py-4 text-slate-600 text-xs font-semibold">
                  {{ $tx->created_at->format('d M Y, H:i') }}
                </td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase rounded-full tracking-widest">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-16 text-center">
                  <div class="mb-3 flex justify-center opacity-40">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                  </div>
                  <p class="text-slate-500 font-bold text-sm">Belum ada data pengumpulan untuk tahun {{ $tahun }}.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($dataMuzakki->hasPages())
      <div class="px-6 py-4 border-t border-slate-200">
        {{ $dataMuzakki->links() }}
      </div>
      @endif

    </div>

  </div>

  {{-- ── SCRIPTS CHART.JS ─────────────────────────────────────────────── --}}
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
      Chart.defaults.color = '#64748b';

      const labels      = @json($bulanLabel);
      const totalBulan  = @json($dataTotalBulan);
      const zakatBulan  = @json($dataZakatBulan);
      const infakBulan  = @json($dataInfakBulan);
      const donutData   = @json($donutData);
      
      const currentEthPrice = {{ $ethPriceIdr }};

      const formatIdr = (val) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
      const formatEth = (val) => new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 4 }).format(val);

      // ── 1. BAR CHART ─────────────────────────────────────────────
      new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Pengumpulan',
            data: totalBulan.map(eth => eth * currentEthPrice), // Ubah sumbu Y ke Rupiah
            backgroundColor: '#10b981',
            hoverBackgroundColor: '#059669',
            borderRadius: 4,
            barPercentage: 0.55,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#1e293b',
              padding: 10,
              callbacks: {
                label: ctx => {
                    let idr = Number(ctx.parsed.y);
                    let eth = idr / currentEthPrice;
                    return ` Rp ${formatIdr(idr)} (≈ ${formatEth(eth)} ETH)`;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f1f5f9' },
              ticks: {
                font: { weight: 'bold', size: 10 },
                callback: val => 'Rp ' + (val >= 1000000 ? (val/1000000).toFixed(1)+'jt' : formatIdr(val))
              }
            },
            x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } }
          }
        }
      });

      // ── 2. LINE CHART ─────────────────────────────────────────────
      new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Zakat',
              data: zakatBulan.map(eth => eth * currentEthPrice),
              borderColor: '#f97316',
              backgroundColor: 'rgba(249,115,22,0.08)',
              pointBackgroundColor: '#f97316',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
            {
              label: 'Infak',
              data: infakBulan.map(eth => eth * currentEthPrice),
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59,130,246,0.08)',
              pointBackgroundColor: '#3b82f6',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: '#1e293b',
              padding: 10,
              callbacks: {
                label: ctx => {
                    let idr = Number(ctx.parsed.y);
                    let eth = idr / currentEthPrice;
                    return ` ${ctx.dataset.label}: Rp ${formatIdr(idr)} (≈ ${formatEth(eth)} ETH)`;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: { color: '#f1f5f9' },
              ticks: {
                font: { weight: 'bold', size: 10 },
                callback: val => 'Rp ' + (val >= 1000000 ? (val/1000000).toFixed(1)+'jt' : formatIdr(val))
              }
            },
            x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } }
          }
        }
      });

      // ── 3. DONUT CHART ────────────────────────────────────────────
      new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
          labels: ['Zakat', 'Infak / Sedekah', 'DSKL', 'Hak Amil'],
          datasets: [{
            data: donutData,
            backgroundColor: ['#059669', '#3b82f6', '#f59e0b', '#64748b'],
            borderWidth: 4,
            borderColor: '#ffffff',
            hoverOffset: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '68%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 16,
                usePointStyle: true,
                pointStyle: 'circle',
                font: { weight: 'bold', size: 10 }
              }
            },
            tooltip: {
              backgroundColor: '#1e293b',
              padding: 10,
              callbacks: {
                label: ctx => {
                    let eth = Number(ctx.parsed);
                    let idr = eth * currentEthPrice;
                    return ` ${ctx.label}: Rp ${formatIdr(idr)} (≈ ${formatEth(eth)} ETH)`;
                }
              }
            }
          }
        }
      });

    });
  </script>
  @endpush

</x-layouts.admin>