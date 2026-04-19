// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

abstract contract ZakatBase {
    // --- State Variables ---
    address public adminKeuangan;
    address public akunPemerintah;
    
    // ✅ PERUBAHAN OPSI 2: Pemisahan Entitas Penyaluran
    address public akunPenyaluran;           // Untuk hak akses & Login Web3
    address payable public dompetPenyaluran; // Khusus Brankas penampung dana
    
    bool public sistemAktif = true;

    // --- Events ---
    event DepositZIS(
        address indexed muzakki,
        uint256 nominal,
        string tipeDana,
        uint256 timestamp
    );
    
    event PencairanDana(
        address indexed admin,
        uint256 nominal,
        address tujuan,
        uint256 timestamp
    );

    // --- Modifiers ---
    modifier hanyaKeuangan() {
        require(msg.sender == adminKeuangan, "Akses Ditolak: Hanya Keuangan!");
        _;
    }

    modifier hanyaPemerintah() {
        require(
            msg.sender == akunPemerintah,
            "Akses Ditolak: Hanya Pemerintah!"
        );
        _;
    }

    modifier saatAktif() {
        require(sistemAktif, "Sistem sedang dibekukan sementara");
        _;
    }
}