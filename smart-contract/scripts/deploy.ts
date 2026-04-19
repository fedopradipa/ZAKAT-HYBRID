import { ethers } from "hardhat";

async function main() {
  // 1. Mengambil akun testing berurutan dari Hardhat (Index 0 sampai 5)
  const [
    deployer,           // Index 0: Yang membayar gas fee
    akunPenyaluran,     // Index 1: Staf Penyaluran (Untuk Login Laravel)
    adminKeuangan,      // Index 2: Staf Keuangan (Untuk Login & Tombol Cairkan)
    akunPemerintah,     // Index 3: Pemerintah/Auditor (Untuk Login)
    dompetPenyaluran,   // Index 4: BRANKAS PASIF (Hanya menerima transferan dana)
    muzakki             // Index 5: Contoh Muzakki pembayar zakat
  ] = await ethers.getSigners();

  console.log("=========================================");
  console.log("🚀 MENGUNGGAH SMART CONTRACT PENGELOLAAN ZAKAT...");
  console.log("Deployer (Pembuat)             :", deployer.address);
  console.log("-----------------------------------------");
  console.log("💼 Akun Login Penyaluran       :", akunPenyaluran.address);
  console.log("💰 Akun Login Keuangan         :", adminKeuangan.address);
  console.log("👨‍⚖️ Akun Login Pemerintah      :", akunPemerintah.address);
  console.log("🏦 Dompet Penampung Penyaluran :", dompetPenyaluran.address);
  console.log("🧑‍💼 Contoh Akun Muzakki        :", muzakki.address);
  console.log("=========================================");

  // 2. Panggil Factory Kontrak UTAMA
  const PengelolaanZakat = await ethers.getContractFactory("PengelolaanZakat");

  // 3. Deploy dengan urutan 4 parameter ke dalam Constructor
  console.log("\n⏳ Sedang melakukan deploy...");
  
  const zakat = await PengelolaanZakat.deploy(
    adminKeuangan.address,
    akunPemerintah.address,
    akunPenyaluran.address,     // <-- Akun Login
    dompetPenyaluran.address    // <-- Dompet Brankas
  );
  
  await zakat.waitForDeployment();

  const contractAddress = await zakat.getAddress();
  console.log("✅ KONTRAK BERHASIL DI-DEPLOY DI ALAMAT:");
  console.log(contractAddress);
  console.log("=========================================");
  console.log("⚠️ PENTING: Salin alamat kontrak ini dan masukkan ke variable CONTRACT_ADDRESS di Laravel Anda!");
}

main().catch((error) => {
  console.error("❌ Terjadi Error saat Deploy:", error);
  process.exitCode = 1;
});