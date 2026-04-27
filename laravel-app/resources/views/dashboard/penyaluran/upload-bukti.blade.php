{{-- resources/views/dashboard/penyaluran/upload-bukti.blade.php --}}

<x-layouts.admin title="Upload Bukti Pelaksanaan">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-black text-slate-800 uppercase tracking-tight">Upload Bukti Pelaksanaan</h1>
      <p class="text-slate-500 text-xs font-semibold mt-1">Foto akan disimpan permanen di jaringan IPFS (tidak bisa dihapus)</p>
    </div>
    <a href="{{ route('penyaluran.konfirmasi') }}"
      class="text-slate-500 hover:text-slate-800 text-xs font-bold uppercase transition-all flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      Kembali
    </a>
  </div>

  {{-- Info Program --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden mb-6">
    <div class="bg-slate-800 px-8 py-4 flex items-center gap-2">
      <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
      <h3 class="text-white font-black uppercase text-xs tracking-widest">Informasi Program</h3>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Judul Program</label>
        <p class="text-sm font-bold text-slate-800">{{ $program->judul }}</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Total Dana Disalurkan</label>
        <p class="text-sm font-mono font-black text-emerald-600">{{ number_format($program->dana_dibutuhkan, 8) }} ETH</p>
      </div>
      <div>
        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Tanggal Pelaksanaan</label>
        <p class="text-sm font-bold text-slate-800">{{ $program->tanggal_pelaksanaan->format('d M Y') }}</p>
      </div>
    </div>
  </div>

  {{-- Form Upload Drag & Drop --}}
  <div class="bg-white rounded-2xl border-2 border-slate-300 shadow-md overflow-hidden">
    <div class="bg-violet-600 px-8 py-4 flex items-center gap-2">
      <svg class="w-4 h-4 text-violet-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      <h3 class="text-white font-black uppercase text-xs tracking-widest">Upload Dokumentasi (Bisa lebih dari 1)</h3>
    </div>

    <div class="p-8">
      {{-- Action dihapus karena kita cegat via Javascript Web3 --}}
      <form id="formBuktiWeb3" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-6">
          <div id="dropzone" class="border-2 border-dashed border-slate-300 rounded-2xl p-10 text-center hover:bg-slate-50 transition-colors cursor-pointer relative">
            <input type="file" name="foto_bukti[]" id="fileInput" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            
            <div class="pointer-events-none">
              <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <p class="text-sm font-bold text-slate-600">Klik atau Drag & Drop foto di sini</p>
              <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, WEBP (Max 5MB/foto)</p>
            </div>
          </div>
        </div>

        {{-- Tempat Preview Gambar --}}
        <div id="previewGrid" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 hidden">
          <!-- Foto preview akan muncul di sini via JS -->
        </div>

        <div class="flex justify-end pt-6 border-t-2 border-slate-100">
          <button type="submit" id="btnUpload" disabled
            class="bg-violet-600 hover:bg-violet-700 text-white font-black py-3 px-8 rounded-xl shadow-lg transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-3 opacity-50 cursor-not-allowed">
            <span id="btnIcon">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            </span>
            <span id="btnText">Upload & Kunci Bukti</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script>
    // ------------------------------------------------------------------------
    // BAGIAN 1: UI LOGIC (DRAG & DROP PREVIEW) - TIDAK ADA YANG DIUBAH!
    // ------------------------------------------------------------------------
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const previewGrid = document.getElementById('previewGrid');
    const btnUpload = document.getElementById('btnUpload');
    const btnIcon = document.getElementById('btnIcon');
    const btnText = document.getElementById('btnText');

    let dataTransfer = new DataTransfer();

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, preventDefaults, false);
      document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
      dropzone.addEventListener(eventName, () => {
        dropzone.classList.add('border-violet-500', 'bg-violet-50');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, () => {
        dropzone.classList.remove('border-violet-500', 'bg-violet-50');
      }, false);
    });

    dropzone.addEventListener('drop', function(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      handleFiles(files);
    });

    fileInput.addEventListener('change', function() {
      handleFiles(this.files);
    });

    function handleFiles(files) {
      for (let i = 0; i < files.length; i++) {
          if (files[i].type.startsWith('image/')) {
              dataTransfer.items.add(files[i]);
          }
      }
      fileInput.files = dataTransfer.files;
      updateUI();
    }

    function updateUI() {
        if (dataTransfer.files.length > 0) {
            previewGrid.classList.remove('hidden');
            btnUpload.disabled = false;
            btnUpload.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            previewGrid.classList.add('hidden');
            btnUpload.disabled = true;
            btnUpload.classList.add('opacity-50', 'cursor-not-allowed');
        }

        previewGrid.innerHTML = '';
        
        Array.from(dataTransfer.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group rounded-xl overflow-hidden border-2 border-slate-200 aspect-square';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <button type="button" class="btn-remove bg-rose-500 text-white p-2 rounded-full hover:bg-rose-600 transition-colors" data-name="${file.name}">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                `;
                previewGrid.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }

    previewGrid.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove');
        if (btn) {
            const fileNameToRemove = btn.getAttribute('data-name');
            const newDataTransfer = new DataTransfer();
            
            for (let file of dataTransfer.files) {
                if (file.name !== fileNameToRemove) {
                    newDataTransfer.items.add(file);
                }
            }
            
            dataTransfer = newDataTransfer;
            fileInput.files = dataTransfer.files;
            updateUI();
        }
    });


    // ------------------------------------------------------------------------
    // ⭐ BAGIAN 2: LOGIKA WEB3 (UPLOAD IPFS -> METAMASK -> DB)
    // ------------------------------------------------------------------------
    const CONTRACT_ADDRESS_GLOBAL = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
    const ABI_PENYALURAN = ["function konfirmasiPenyaluranData(uint256 _programId, string _buktiHash) public"];

    // Kumpulan SVG Icons (sebagai string)
    const iconUploadBase = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>';
    const iconIpfs       = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>';
    const iconMetaMask   = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>';
    const iconLoading    = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    const iconDatabase   = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>';
    const iconSuccess    = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

    document.getElementById('formBuktiWeb3').addEventListener('submit', async function(e) {
        e.preventDefault(); 

        if(dataTransfer.files.length === 0) {
            alert("Silakan masukkan minimal 1 foto bukti pelaksanaan!");
            return;
        }

        if (!window.ethereum) {
            return alert("Peringatan: MetaMask tidak ditemukan di browser Anda!");
        }

        try {
            // Kunci Tombol
            btnUpload.disabled = true;
            btnUpload.classList.add('opacity-75', 'cursor-not-allowed');

            // --- TAHAP 1: UPLOAD FOTO KE IPFS ---
            btnIcon.innerHTML = iconIpfs;
            btnText.innerText = 'Mengupload ke IPFS...';

            const formData = new FormData();
            for (let i = 0; i < dataTransfer.files.length; i++) {
                formData.append('foto_bukti[]', dataTransfer.files[i]);
            }
            
            // Tembak ke fungsi prepareBuktiWeb3 di Controller Penyaluran
            const prepareUrl = "{{ route('penyaluran.prepare_bukti', $program->id) }}";
            const prepareRes = await fetch(prepareUrl, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: formData 
            });

            const prepareData = await prepareRes.json();
            
            if (prepareData.status !== 'success') {
                throw new Error(prepareData.message || "Gagal upload ke IPFS.");
            }

            const ipfsHashesString = prepareData.bukti_ipfs_hash; 
            console.log("Status IPFS OK:", ipfsHashesString);


            // --- TAHAP 2: EKSEKUSI METAMASK ---
            btnIcon.innerHTML = iconMetaMask;
            btnText.innerText = 'Menunggu Tanda Tangan...';

            const provider = new ethers.BrowserProvider(window.ethereum);
            const signer = await provider.getSigner();
            const contract = new ethers.Contract(CONTRACT_ADDRESS_GLOBAL, ABI_PENYALURAN, signer);

            btnIcon.innerHTML = iconLoading;
            btnText.innerText = 'Memproses Blockchain...';

            const tx = await contract.konfirmasiPenyaluranData("{{ $program->id }}", ipfsHashesString);

            btnText.innerText = 'Validasi Jaringan...';
            await tx.wait();
            console.log("Tx Blockchain Sukses:", tx.hash);


            // --- TAHAP 3: SIMPAN KE MYSQL ---
            btnIcon.innerHTML = iconDatabase;
            btnText.innerText = 'Sinkronisasi Database...';
            
            const submitUrl = "{{ route('penyaluran.submit_konfirmasi', $program->id) }}";
            const submitRes = await fetch(submitUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    tx_hash: tx.hash,
                    bukti_ipfs_hash: ipfsHashesString 
                })
            });

            const submitData = await submitRes.json();

            if (submitData.status === 'success') {
                btnIcon.innerHTML = iconSuccess;
                btnText.innerText = 'Berhasil Terkunci!';
                alert("Alhamdulillah, Foto Bukti berhasil dikunci secara permanen di Blockchain!");
                window.location.href = "{{ route('penyaluran.konfirmasi') }}";
            } else {
                throw new Error(submitData.message || "Gagal update database MySQL.");
            }

        } catch (error) {
            console.error(error);
            
            // Kembalikan tombol ke semula jika error
            btnUpload.disabled = false;
            btnUpload.classList.remove('opacity-75', 'cursor-not-allowed');
            btnIcon.innerHTML = iconUploadBase;
            btnText.innerText = 'Upload & Kunci Bukti';

            if (error.code === "ACTION_REJECTED") {
                alert("Transaksi dibatalkan oleh Anda di MetaMask.");
            } else if (error.message && error.message.includes("Dana belum cair")) {
                alert("Program ini belum cair atau fotonya sudah pernah diupload ke Blockchain.");
            } else if (error.message && error.message.includes("Hanya Tim Penyaluran")) {
                alert("Dompet Anda tidak terdaftar sebagai Admin Penyaluran.");
            } else {
                alert("Error: " + (error.reason || error.message || "Terjadi kesalahan server."));
            }
        }
    });
  </script>
  @endpush
</x-layouts.admin>