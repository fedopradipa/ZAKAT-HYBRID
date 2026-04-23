// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

abstract contract ZakatBase {
    // --- State Variables (TIDAK DIUBAH) ---
    address public adminKeuangan;
    address public akunPemerintah;
    address public akunPenyaluran;
    address payable public dompetPenyaluran;
    bool public sistemAktif = true;

    // ── BARU: Pemisahan saldo ──────────────────────────────────────
    uint256 public saldoZIS;   // 87.5% dari setiap deposit
    uint256 public saldoAmil;  // 12.5% dari setiap deposit (ditampung)

    // Konstanta: 125/1000 = 12.5%
    uint256 public constant PORSI_AMIL  = 125;
    uint256 public constant DENOMINATOR = 1000;

    // --- Events ---
    // DepositZIS: tambah 2 parameter baru (nominalBersih & hakAmil)
    event DepositZIS(
        address indexed muzakki,
        uint256 nominalTotal,
        uint256 nominalBersih,
        uint256 hakAmil,
        string  tipeDana,
        uint256 timestamp
    );

    // PencairanDana: TIDAK DIUBAH
    event PencairanDana(
        address indexed admin,
        uint256 nominal,
        address tujuan,
        uint256 timestamp
    );

    // UpdateDompetPenyaluran: TIDAK DIUBAH
    event UpdateDompetPenyaluran(
        address indexed dompetLama,
        address indexed dompetBaru,
        uint256 timestamp
    );

    // UpdateAkunPenyaluran: TIDAK DIUBAH
    event UpdateAkunPenyaluran(
        address indexed akunLama,
        address indexed akunBaru,
        uint256 timestamp
    );

    // --- Modifiers (TIDAK DIUBAH) ---
    modifier hanyaKeuangan() {
        require(msg.sender == adminKeuangan, "Akses Ditolak: Hanya Keuangan!");
        _;
    }

    modifier hanyaPemerintah() {
        require(msg.sender == akunPemerintah, "Akses Ditolak: Hanya Pemerintah!");
        _;
    }

    modifier saatAktif() {
        require(sistemAktif, "Sistem sedang dibekukan sementara");
        _;
    }
}