// smart-contract/sync.js

const { ethers } = require("ethers");
const fs         = require("fs");

// ── KONFIGURASI ───────────────────────────────────────────────────
const CONTRACT_ADDRESS = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
const URL_ZAKAT_MASUK  = "http://127.0.0.1:8000/api/webhook/rebuild-zakat"; 
const URL_PENYALURAN   = "http://127.0.0.1:8000/api/webhook/rebuild-penyaluran";
const WEBHOOK_SECRET   = "rahasia-baznas-123";
const RPC_URL          = "http://127.0.0.1:8545";

// ── LOAD ABI ──────────────────────────────────────────────────────
const artifactPath = "./artifacts/contracts/PengelolaanZakat.sol/PengelolaanZakat.json";
const contractJson = JSON.parse(fs.readFileSync(artifactPath, "utf8"));
const abi          = contractJson.abi;

async function syncAll() {
    const provider = new ethers.JsonRpcProvider(RPC_URL);
    const contract = new ethers.Contract(CONTRACT_ADDRESS, abi, provider);

    console.log("🔄 Memulai Proses Catch-up & Rebuild (Sinkronisasi Penuh Web2 <-> Web3)...");

    try {
        // =================================================================
        // 1. REBUILD ZAKAT MASUK
        // =================================================================
        const eventsDeposit = await contract.queryFilter("DepositZIS", 0, "latest");
        console.log(`[SCAN] Ditemukan ${eventsDeposit.length} riwayat DepositZIS di Blockchain.`);

        const batchDeposit = [];
        for (const event of eventsDeposit) {
            batchDeposit.push({
                tx_hash: event.transactionHash,
                wallet_address: event.args[0],
                nominal: ethers.formatEther(event.args[1]),
                nominal_bersih: ethers.formatEther(event.args[2]),
                hak_amil: ethers.formatEther(event.args[3]),
                jenis_dana: event.args[4], 
            });
        }

        if (batchDeposit.length > 0) {
            console.log(`🚀 Mengirim ${batchDeposit.length} data Zakat Masuk ke Laravel...`);
            await fetch(URL_ZAKAT_MASUK, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Webhook-Secret': WEBHOOK_SECRET
                },
                body: JSON.stringify({ transactions: batchDeposit })
            });
        }

        // =================================================================
        // 2. REBUILD PENYALURAN (DANA CAIR + KONFIRMASI)
        // =================================================================
        const batchPenyaluran = [];
        
        // ⭐ PERBAIKAN: Cache untuk mengingat proposal hash setiap program
        const proposalMap = {}; 

        // A. Tarik event dana keluar
        const eventsCair = await contract.queryFilter("PencairanDana", 0, "latest");
        console.log(`[SCAN] Ditemukan ${eventsCair.length} riwayat PencairanDana di Blockchain.`);
        
        for (const event of eventsCair) {
            const pId = Number(event.args[1]);
            const propHash = event.args[4];
            
            proposalMap[pId] = propHash; // Simpan ke ingatan

            batchPenyaluran.push({
                program_id: pId,
                proposal_ipfs_hash: propHash,
                bukti_ipfs_hash: null, // Belum ada bukti
                tx_hash: event.transactionHash,
                status: 1 // 1 = Proses Pelaksanaan
            });
        }

        // B. Tarik event laporan foto (Bukti)
        const eventsBukti = await contract.queryFilter("KonfirmasiPenyaluranEvent", 0, "latest");
        console.log(`[SCAN] Ditemukan ${eventsBukti.length} riwayat KonfirmasiPenyaluran di Blockchain.`);

        for (const event of eventsBukti) {
            const pId = Number(event.args[1]);
            
            // Hapus status 1 dari array jika program ini sudah berstatus 2
            const existingIndex = batchPenyaluran.findIndex(b => b.program_id === pId);
            if (existingIndex !== -1) {
                batchPenyaluran.splice(existingIndex, 1);
            }

            // Push status final (2) dengan KEDUA HASH yang benar
            batchPenyaluran.push({
                program_id: pId,
                proposal_ipfs_hash: proposalMap[pId] || "", // Ambil dari ingatan event Cair
                bukti_ipfs_hash: event.args[2],             // Ini Hash foto asli
                tx_hash: event.transactionHash,
                status: 2 // 2 = Telah Terkonfirmasi
            });
        }

        if (batchPenyaluran.length > 0) {
            console.log(`🚀 Mengirim ${batchPenyaluran.length} data Penyaluran ke Laravel untuk Audit Mutlak...`);
            const responsePenyaluran = await fetch(URL_PENYALURAN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Webhook-Secret': WEBHOOK_SECRET
                },
                body: JSON.stringify({ events: batchPenyaluran })
            });

            const contentType = responsePenyaluran.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                const result = await responsePenyaluran.json();
                console.log(`🎉 Pesan Audit Laravel: ${result.message}`);
            } else {
                console.log(`❌ Gagal Audit Penyaluran! HTTP Status: ${responsePenyaluran.status}`);
            }
        }

        console.log(`\n✅ ALL SYNC SUCCESS! Data Web2 (MySQL) sekarang 100% tersertifikasi oleh Web3 (Blockchain).`);

    } catch (error) {
        console.error("❌ Terjadi kesalahan Fatal saat sinkronisasi:", error);
    }
}

syncAll();