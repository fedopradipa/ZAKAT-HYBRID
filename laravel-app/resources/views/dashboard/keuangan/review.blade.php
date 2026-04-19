{{-- resources/views/dashboard/keuangan/review.blade.php --}}

<x-layouts.admin title="Review Pencairan Dana">
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-xl font-bold text-slate-800 uppercase tracking-tight">Cairkan Dana Program Penyaluran</h1>
    <a href="{{ route('keuangan.pengajuan') }}"
      class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase transition-all flex items-center gap-2">
      ← Kembali ke Daftar
    </a>
  </div>

  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden mb-10">
    <div class="bg-[#5c8a06] px-8 py-4">
      <h3 class="text-white font-black uppercase text-xs tracking-widest">📝 Detail Review Program Penyaluran</h3>
    </div>

    <div class="p-8 space-y-6">

      @if(session('error'))
      <div class="bg-rose-100 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl">
        <p class="font-black text-xs uppercase">{{ session('error') }}</p>
      </div>
      @endif

      {{-- Judul --}}
      <div class="md:col-span-2">
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Judul Program</label>
        <input
          type="text"
          disabled
          value="{{ $program->judul }}"
          class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
      </div>

      {{-- Deskripsi --}}
      <div>
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Deskripsi Lengkap</label>
        <textarea
          disabled
          rows="3"
          class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed resize-none">{{ $program->deskripsi }}</textarea>
      </div>

      {{-- Tanggal & Dana --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Tanggal Pelaksanaan</label>
          <input
            type="text"
            disabled
            value="{{ $program->tanggal_pelaksanaan->format('d/m/Y') }}"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
        </div>
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Dana Dibutuhkan (ETH)</label>
          <input
            type="text"
            disabled
            value="{{ number_format($program->dana_dibutuhkan, 8) }} ETH"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-mono font-black text-emerald-700 bg-slate-50 cursor-not-allowed">
        </div>
      </div>

      {{-- Bidang & Sumber Dana --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Bidang Penyaluran</label>
          <input
            type="text"
            disabled
            value="{{ $program->bidang }}"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
        </div>
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Sumber Dana</label>
          <input
            type="text"
            disabled
            value="{{ $program->sumber_dana }}"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
        </div>
      </div>

      {{-- Asnaf & Bentuk Bantuan --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Jenis Golongan Asnaf</label>
          <input
            type="text"
            disabled
            value="{{ $program->asnaf }}"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
        </div>
        <div>
          <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Bentuk Bantuan</label>
          <input
            type="text"
            disabled
            value="{{ $program->bentuk_bantuan }}"
            class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-900 bg-slate-50 cursor-not-allowed">
        </div>
      </div>

      {{-- Deskripsi Mustahik --}}
      <div>
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Deskripsi Umum Mustahik</label>
        <textarea
          disabled
          rows="2"
          class="w-full border-2 border-slate-300 rounded-lg py-3 px-4 text-sm font-semibold text-slate-700 bg-slate-50 cursor-not-allowed resize-none italic">{{ $program->deskripsi_mustahik }}</textarea>
      </div>

      {{-- Tipe Mustahik Badge --}}
      <div>
        <label class="block text-[11px] font-black text-slate-700 uppercase tracking-widest mb-2">Jenis Data Mustahik</label>
        @if($program->tipe_mustahik === 'detail')
        <span class="inline-flex items-center gap-2 bg-amber-100 text-amber-800 text-[10px] font-black uppercase px-4 py-2 rounded-full border-2 border-amber-400">
          📋 Detail (Tabel NIK & Nama)
        </span>
        @else
        <span class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 text-[10px] font-black uppercase px-4 py-2 rounded-full border-2 border-slate-300">
          📝 Umum (Deskripsi)
        </span>
        @endif
      </div>

      {{-- TABEL MUSTAHIK DETAIL --}}
      @if($program->tipe_mustahik === 'detail' && $program->mustahiks->isNotEmpty())
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-widest underline decoration-amber-500 decoration-2">
            Daftar Rincian Penerima Manfaat
          </h4>
          <span class="bg-emerald-100 text-emerald-800 text-[10px] font-black px-4 py-1.5 rounded-full border-2 border-emerald-300">
            Total: {{ $program->mustahiks->count() }} Mustahik
          </span>
        </div>

        <div class="overflow-x-auto border-2 border-slate-300 rounded-xl shadow-sm">
          <table class="w-full text-left border-collapse bg-white">
            <thead class="bg-slate-800 text-[10px] font-black text-white uppercase">
              <tr>
                <th class="px-4 py-3 w-12 text-center">No</th>
                <th class="px-4 py-3">NIK</th>
                <th class="px-4 py-3">Nama Lengkap</th>
                <th class="px-4 py-3">Bantuan</th>
                <th class="px-4 py-3">Alamat</th>
              </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100">
              @foreach($program->mustahiks as $index => $mustahik)
              <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}">
                <td class="px-4 py-3 text-center font-black text-slate-600 text-xs">{{ $index + 1 }}</td>
                <td class="px-4 py-3 text-xs font-mono font-black text-slate-800">{{ $mustahik->nik }}</td>
                <td class="px-4 py-3 text-xs font-black text-slate-900">{{ $mustahik->nama }}</td>
                <td class="px-4 py-3 text-xs font-semibold text-slate-700">{{ $mustahik->bentuk_bantuan }}</td>
                <td class="px-4 py-3 text-xs font-semibold text-slate-700">{{ $mustahik->alamat }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

      {{-- Tombol Cairkan --}}
      <div class="flex justify-end pt-6 border-t-2 border-slate-200">
        <button type="button" id="btnCairkan"
          class="bg-[#5c8a06] hover:bg-[#4a6f05] text-white font-black py-4 px-14 rounded-xl shadow-lg transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-3">
          <span id="btnIcon">💎</span>
          <span id="btnText">Submit & Cairkan Dana</span>
        </button>
      </div>

    </div>
  </div>

  @push('scripts')
  <script>
    const CONTRACT_ADDRESS_GLOBAL = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
    const ABI_KEUANGAN = ["function cairkanDana(uint256 _nominal) public"];

    document.getElementById('btnCairkan').addEventListener('click', async (e) => {
      e.preventDefault();
      const btn = e.currentTarget;
      const btnText = document.getElementById('btnText');
      const btnIcon = document.getElementById('btnIcon');

      if (!window.ethereum) {
        return alert("⚠️ MetaMask tidak ditemukan!");
      }

      const nominalEth = "{{ $program->dana_dibutuhkan }}";
      const programId = "{{ $program->id }}";

      try {
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-[#4a6f05]', 'active:scale-95');
        btnText.innerText = "Menunggu MetaMask...";
        btnIcon.innerText = "⏳";

        const provider = new ethers.BrowserProvider(window.ethereum);
        const signer = await provider.getSigner();
        const contract = new ethers.Contract(CONTRACT_ADDRESS_GLOBAL, ABI_KEUANGAN, signer);
        const nominalWei = ethers.parseEther(nominalEth.toString());

        btnText.innerText = "Memproses Transaksi...";
        const tx = await contract.cairkanDana(nominalWei);

        btnText.innerText = "Validasi Blockchain...";
        await tx.wait();

        btnText.innerText = "Sinkronisasi Data...";
        const response = await fetch(`/keuangan/pengajuan/${programId}/approve`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json"
          },
          body: JSON.stringify({
            tx_hash: tx.hash
          })
        });

        const data = await response.json();

        if (data.status === 'success') {
          btnIcon.innerText = "✅";
          btnText.innerText = "Dana Berhasil Dicairkan!";
          btn.classList.replace('bg-[#5c8a06]', 'bg-emerald-600');
          alert("Alhamdulillah, transaksi sukses!\nTx Hash: " + tx.hash);
          window.location.href = "{{ route('keuangan.pengajuan') }}";
        } else {
          throw new Error("Gagal mengupdate database.");
        }

      } catch (error) {
        console.error(error);
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        btn.classList.add('hover:bg-[#4a6f05]', 'active:scale-95');
        btnText.innerText = "Submit & Cairkan Dana";
        btnIcon.innerText = "💎";

        if (error.code === "ACTION_REJECTED") {
          alert("❌ Transaksi dibatalkan oleh Anda.");
        } else if (error.message.includes("Saldo tidak cukup")) {
          alert("❌ Saldo Brankas Zakat tidak mencukupi.");
        } else if (error.message.includes("Hanya Keuangan")) {
          alert("❌ Dompet Anda tidak terdaftar sebagai Admin Keuangan.");
        } else {
          alert("❌ Error: " + (error.reason || error.message));
        }
      }
    });
  </script>
  @endpush
</x-layouts.admin>