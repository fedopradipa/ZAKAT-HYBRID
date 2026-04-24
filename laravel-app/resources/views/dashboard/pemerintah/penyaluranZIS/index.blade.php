<x-layouts.admin title="Penyaluran ZIS DSKL">

  @push('styles')
  <style>
    .animate-fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  </style>
  @endpush

  <div class="space-y-6 animate-fade-in-up">

    {{-- ── HEADER + FILTER ─────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <h1 class="text-xl font-black text-slate-800 tracking-tight">PENYALURAN ZIS DSKL</h1>

      <form method="GET" action="{{ route('pemerintah.penyaluran_zis_dskl') }}" class="flex items-center gap-2">
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

    {{-- ── SUB HEADER ───────────────────────────────────────────────── --}}
    <p class="text-sm font-semibold text-slate-700">
      Program Penyaluran Pada Tahun {{ $tahun }}
    </p>

    {{-- ── 6 STATUS CARDS ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">

      {{-- Total Program --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-slate-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-slate-800">{{ $totalProgram }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Total Program</p>
      </div>

      {{-- Belum Dicairkan --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-blue-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-blue-600">{{ $belumCair }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Belum Dicairkan</p>
      </div>

      {{-- Proses Pelaksanaan --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-amber-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-amber-500">{{ $prosesPelaksanaan }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Proses Pelaksanaan</p>
      </div>

      {{-- Tidak Terlaksana --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-red-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-red-500">{{ $tidakTerlaksana }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Tidak Terlaksana</p>
      </div>

      {{-- Belum Dikonfirmasi --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-orange-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-orange-500">{{ $belumDikonfirmasi }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Belum Dikonfirmasi</p>
      </div>

      {{-- Telah Dikonfirmasi --}}
      <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-center hover:border-emerald-300 transition-colors">
        <div class="flex justify-center mb-2">
          <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-black text-emerald-600">{{ $telahTerkonfirmasi }}</p>
        <p class="text-[11px] text-slate-500 font-semibold mt-1">Telah Dikonfirmasi</p>
      </div>

    </div>

    {{-- ── ROW 2: Donut Chart + Tabel 5 Program Terakhir ───────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

      {{-- Donut Chart --}}
      <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
        <h3 class="font-black text-slate-800 text-sm mb-4">Rasio Berdasarkan Status Program</h3>
        <div class="relative flex-1 flex justify-center items-center min-h-[240px]">
          <canvas id="donutChart"></canvas>
        </div>
      </div>

      {{-- Tabel 5 Program Terakhir --}}
      <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
          <h3 class="font-black text-slate-800 text-sm">Update Lima Program Terakhir</h3>
        </div>

        <div class="overflow-x-auto flex-1">
          <table class="w-full text-xs">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50">
                <th class="px-4 py-3 text-left font-black text-slate-500 uppercase tracking-wider">Judul</th>
                <th class="px-4 py-3 text-left font-black text-slate-500 uppercase tracking-wider">Rencana Pelaksanaan</th>
                <th class="px-4 py-3 text-left font-black text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-right font-black text-slate-500 uppercase tracking-wider">Dana</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($programTerakhir as $program)
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-4 py-3 text-slate-700 font-semibold max-w-[180px]">
                  <p class="truncate">{{ $program->judul }}</p>
                </td>
                <td class="px-4 py-3 text-slate-500">
                  {{ \Carbon\Carbon::parse($program->tanggal_pelaksanaan)->format('j/n/Y') }}
                </td>
                <td class="px-4 py-3">
                  @php
                    $statusMap = [
                      'belum_cair'          => ['label' => 'Belum dicairkan',   'class' => 'bg-blue-50 text-blue-600 border-blue-200'],
                      'proses_pelaksanaan'  => ['label' => 'Proses pelaksanaan','class' => 'bg-amber-50 text-amber-600 border-amber-200'],
                      'tidak_terlaksana'    => ['label' => 'Tidak terlaksana',  'class' => 'bg-red-50 text-red-600 border-red-200'],
                      'belum_dikonfirmasi'  => ['label' => 'Belum dikonfirmasi','class' => 'bg-orange-50 text-orange-600 border-orange-200'],
                      'telah_terkonfirmasi' => ['label' => 'Telah terkonfirmasi','class'=> 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    ];
                    $s = $statusMap[$program->status] ?? ['label' => $program->status, 'class' => 'bg-slate-50 text-slate-600 border-slate-200'];
                  @endphp
                  <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $s['class'] }}">
                    {{ $s['label'] }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right font-bold text-emerald-700">
                  Rp {{ number_format($program->dana_idr, 0, ',', '.') }}
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-xs">
                  Belum ada data program untuk tahun {{ $tahun }}.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

    {{-- ── LINE CHART: Jumlah Program per Bulan per Status ────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <h3 class="font-black text-slate-800 text-sm mb-5">Tren Jumlah Program per Bulan</h3>
      <div class="relative h-72 w-full">
        <canvas id="lineChart"></canvas>
      </div>
    </div>

  </div>

  {{-- ── SCRIPTS ──────────────────────────────────────────────────────── --}}
  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
      Chart.defaults.color = '#64748b';

      const labels = @json($bulanLabel);
      const lineData = @json($lineData);
      const donutData = @json($donutData);
      const donutLabels = @json($donutLabels);

      // ── DONUT CHART ──────────────────────────────────────────────
      new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
          labels: donutLabels,
          datasets: [{
            data: donutData,
            backgroundColor: [
              '#3b82f6', // Belum Dicairkan   - blue
              '#f59e0b', // Proses Pelaksanaan - amber
              '#ef4444', // Tidak Terlaksana   - red
              '#f97316', // Belum Dikonfirmasi - orange
              '#10b981', // Telah Dikonfirmasi - emerald
            ],
            borderWidth: 4,
            borderColor: '#ffffff',
            hoverOffset: 4,
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
                padding: 12,
                usePointStyle: true,
                pointStyle: 'circle',
                font: { weight: 'bold', size: 10 }
              }
            },
            tooltip: {
              backgroundColor: '#1e293b',
              padding: 10,
              callbacks: {
                label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' program'
              }
            }
          }
        }
      });

      // ── LINE CHART ───────────────────────────────────────────────
      new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Belum Dicairkan',
              data: lineData.belum_cair,
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59,130,246,0.08)',
              pointBackgroundColor: '#3b82f6',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
            {
              label: 'Proses Pelaksanaan',
              data: lineData.proses_pelaksanaan,
              borderColor: '#f59e0b',
              backgroundColor: 'rgba(245,158,11,0.08)',
              pointBackgroundColor: '#f59e0b',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
            {
              label: 'Tidak Terlaksana',
              data: lineData.tidak_terlaksana,
              borderColor: '#ef4444',
              backgroundColor: 'rgba(239,68,68,0.08)',
              pointBackgroundColor: '#ef4444',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
            {
              label: 'Belum Dikonfirmasi',
              data: lineData.belum_dikonfirmasi,
              borderColor: '#f97316',
              backgroundColor: 'rgba(249,115,22,0.08)',
              pointBackgroundColor: '#f97316',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
            {
              label: 'Telah Dikonfirmasi',
              data: lineData.telah_terkonfirmasi,
              borderColor: '#10b981',
              backgroundColor: 'rgba(16,185,129,0.08)',
              pointBackgroundColor: '#10b981',
              pointRadius: 4,
              tension: 0.3,
              fill: false,
            },
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            mode: 'index',
            intersect: false,
          },
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
                label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' program'
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: { weight: 'bold', size: 10 }
              },
              grid: { color: '#f1f5f9' }
            },
            x: {
              grid: { display: false },
              ticks: { font: { weight: 'bold', size: 10 } }
            }
          }
        }
      });

    });
  </script>
  @endpush

</x-layouts.admin>