<x-layouts.portal title="BAZNAS - Mari Tunaikan Zakat">

  <section class="max-w-7xl mx-auto px-6 py-12 md:py-20 grid md:grid-cols-2 gap-12 items-center">
    <div class="space-y-6">
      <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-tight">
        Mari Tunaikan Zakat
      </h1>
      <p class="text-slate-500 italic text-lg leading-relaxed max-w-lg border-l-4 border-emerald-500 pl-5 font-medium">
        "Ambillah zakat dari harta mereka, guna membersihkan dan menyucikan mereka, dan berdoalah untuk mereka. Sesungguhnya doamu itu (menumbuhkan) ketenteraman jiwa bagi mereka. Allah Maha Mendengar, Maha Mengetahui."
        <br>
        <span class="text-sm font-bold text-emerald-600 not-italic mt-2 block">[QS. At-Taubah Ayat 103]</span>
      </p>
      <div class="flex gap-4">
        <a href="{{ route('zakat.form') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl transition-all shadow-lg shadow-emerald-200 text-lg">
          Mulai Berzakat
        </a>
        <button class="bg-white border-2 border-slate-100 hover:border-emerald-100 text-slate-600 font-bold py-4 px-10 rounded-2xl transition-all text-lg">
          Lihat Program
        </button>
      </div>
    </div>

    <div class="relative flex justify-center">
      <div class="w-full max-w-md bg-emerald-50 aspect-square rounded-full absolute -z-10 animate-pulse"></div>

      <img src="https://img.freepik.com/free-vector/charity-concept-illustration_114360-5394.jpg"
        alt="Zakat Illustration"
        class="w-full max-w-lg drop-shadow-2xl rounded-[3rem]">
    </div>
  </section>

  @push('scripts')
  <script>
    // Ambil elemen tombol connect dari Navbar (karena portal-navbar menggunakan id ini)
    const btnConnectPortal = document.getElementById('btnConnect');

    // Fungsi connectAndAuth sudah tersedia secara global di portal.blade.php
    // Kita hanya perlu memastikan event listener terpasang jika tombol ada
    if (btnConnectPortal) {
      btnConnectPortal.addEventListener('click', async (e) => {
        e.preventDefault();
        await connectAndAuth();
      });
    }
  </script>
  @endpush

</x-layouts.portal>