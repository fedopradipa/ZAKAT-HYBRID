// smart-contract/listener.js

const { ethers } = require("ethers");
const fs         = require("fs");

// ── KONFIGURASI ───────────────────────────────────────────────────
const CONTRACT_ADDRESS = "0x5FbDB2315678afecb367f032d93F642f64180aa3";
const URL_ZAKAT_MASUK  = "http://127.0.0.1:8000/api/webhook/verify-zakat";
const URL_PENYALURAN   = "http://127.0.0.1:8000/api/webhook/rebuild-penyaluran";
const WEBHOOK_SECRET   = "rahasia-baznas-123";
const WS_URL           = "ws://127.0.0.1:8545"; // ⭐ WebSocket (bukan HTTP polling)

// ── LOAD ABI ──────────────────────────────────────────────────────
const artifactPath = "./artifacts/contracts/PengelolaanZakat.sol/PengelolaanZakat.json";
const contractJson = JSON.parse(fs.readFileSync(artifactPath, "utf8"));
const abi          = contractJson.abi;

// ── RECONNECT LOGIC ───────────────────────────────────────────────
let provider = null;
let contract  = null;
let isRunning = false;

function createProvider() {
    // ⭐ WebSocketProvider: push-based, tidak polling
    // Solusi untuk: "TypeError: results is not iterable"
    // yang terjadi karena Hardhat return null saat eth_getFilterChanges
    return new ethers.WebSocketProvider(WS_URL);
}

async function startListener() {
    if (isRunning) return;
    isRunning = true;

    try {
        provider = createProvider();
        contract  = new ethers.Contract(CONTRACT_ADDRESS, abi, provider);

        console.log("====================================================");
        console.log("  BAZNAS Event Listener - AKTIF (WEBSOCKET MODE)");
        console.log("  Contract :", CONTRACT_ADDRESS);
        console.log("  WS URL   :", WS_URL);
        console.log("====================================================");
        console.log("Menunggu seluruh event dari Blockchain...\n");

        // ── 1. DENGARKAN EVENT DepositZIS (UANG MASUK) ────────────
        contract.on("DepositZIS", (muzakki, nominalTotal, nominalBersih, hakAmil, tipeDana, timestamp, event) => {
            const txHash = event.log.transactionHash;
            console.log("----------------------------------------------------");
            console.log("[EVENT MASUK] DepositZIS terdeteksi!");
            console.log("  TX Hash     :", txHash);
            console.log("----------------------------------------------------");

            const payload = {
                tx_hash:        txHash,
                wallet_address: muzakki,
                nominal:        ethers.formatEther(nominalTotal),
                nominal_bersih: ethers.formatEther(nominalBersih),
                hak_amil:       ethers.formatEther(hakAmil),
                jenis_dana:     tipeDana,
                timestamp:      timestamp.toString(),
            };

            kirimKeLaravel(payload, URL_ZAKAT_MASUK);
        });

        // ── 2. DENGARKAN EVENT PencairanDana (UANG KELUAR) ────────
        contract.on("PencairanDana", (admin, programId, nominal, tujuan, proposalHash, timestamp, event) => {
            const txHash = event.log.transactionHash;
            console.log("----------------------------------------------------");
            console.log("[EVENT KELUAR] PencairanDana terdeteksi!");
            console.log("  Program ID  :", Number(programId));
            console.log("----------------------------------------------------");

            const payload = {
                events: [{
                    program_id: Number(programId),
                    proposal_ipfs_hash: proposalHash,
                    tx_hash: txHash,
                    status: 1 // 1 = Proses Pelaksanaan
                }]
            };

            kirimKeLaravel(payload, URL_PENYALURAN);
        });

        // ── 3. DENGARKAN EVENT KonfirmasiPenyaluranEvent (BUKTI) ──
        contract.on("KonfirmasiPenyaluranEvent", (amil, programId, buktiHash, timestamp, event) => {
            const txHash = event.log.transactionHash;
            console.log("----------------------------------------------------");
            console.log("[EVENT BUKTI] KonfirmasiPenyaluran terdeteksi!");
            console.log("  Program ID  :", Number(programId));
            console.log("----------------------------------------------------");

            const payload = {
                events: [{
                    program_id: Number(programId),
                    proposal_ipfs_hash: buktiHash,
                    tx_hash: txHash,
                    status: 2 // 2 = Telah Terkonfirmasi
                }]
            };

            kirimKeLaravel(payload, URL_PENYALURAN);
        });

        // ── DETEKSI KONEKSI PUTUS ─────────────────────────────────
        provider.on("error", (err) => {
            console.error("[ERROR] Provider error:", err.message);
            reconnect();
        });

        // ⭐ Handle WebSocket disconnect (Hardhat restart, network drop, dsb)
        provider.websocket.on("close", () => {
            console.warn("[WS] Koneksi WebSocket terputus. Reconnecting...");
            reconnect();
        });

    } catch (err) {
        console.error("[FATAL] Gagal start listener:", err.message);
        isRunning = false;
        setTimeout(startListener, 5000);
    }
}

function reconnect() {
    isRunning = false;
    if (contract) {
        contract.removeAllListeners();
        contract = null;
    }
    // ⭐ Destroy WebSocket dengan benar agar port tidak menggantung
    if (provider) {
        try { provider.destroy(); } catch (_) {}
        provider = null;
    }
    console.log("[RECONNECT] Mencoba reconnect dalam 5 detik...");
    setTimeout(startListener, 5000);
}

// ── KIRIM DATA KE LARAVEL VIA WEBHOOK DINAMIS ────────────────────
async function kirimKeLaravel(payload, urlTarget, retryCount = 0) {
    const MAX_RETRY = 5;

    try {
        const response = await fetch(urlTarget, {
            method:  "POST",
            headers: {
                "Content-Type":     "application/json",
                "X-Webhook-Secret": WEBHOOK_SECRET,
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (response.ok) {
            console.log(`[WEBHOOK SUKSES] -> ${urlTarget}:`, data.message);
        } else {
            console.error(`[WEBHOOK DITOLAK] -> ${urlTarget}:`, data.message || "Error");

            if (response.status === 409) {
                console.warn("[WEBHOOK] Transaksi sudah ada di DB, skip.");
                return;
            }

            if (retryCount < MAX_RETRY) {
                const delay = (retryCount + 1) * 3000;
                console.log(`[WEBHOOK] Retry ke-${retryCount + 1} dalam ${delay/1000} detik...`);
                setTimeout(() => kirimKeLaravel(payload, urlTarget, retryCount + 1), delay);
            } else {
                console.error("[WEBHOOK] Gagal total setelah", MAX_RETRY, "retry.");
                fs.appendFileSync(
                    "./failed_events.log",
                    JSON.stringify({ target: urlTarget, ...payload, failed_at: new Date().toISOString() }) + "\n"
                );
            }
        }
    } catch (err) {
        console.error("[WEBHOOK] Koneksi gagal:", err.message);

        if (retryCount < MAX_RETRY) {
            const delay = (retryCount + 1) * 3000;
            console.log(`[WEBHOOK] Retry ke-${retryCount + 1} dalam ${delay/1000} detik...`);
            setTimeout(() => kirimKeLaravel(payload, urlTarget, retryCount + 1), delay);
        } else {
            fs.appendFileSync(
                "./failed_events.log",
                JSON.stringify({ target: urlTarget, ...payload, failed_at: new Date().toISOString() }) + "\n"
            );
        }
    }
}

// ── START ─────────────────────────────────────────────────────────
startListener();