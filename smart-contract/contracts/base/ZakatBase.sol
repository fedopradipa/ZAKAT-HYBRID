// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

abstract contract ZakatBase {
    // --- State Variables ---
    address public adminKeuangan;
    address public akunPemerintah;
    address public akunPenyaluran;
    address payable public dompetPenyaluran;
    bool public sistemAktif = true;

    // --- Pemisahan saldo ---
    uint256 public saldoZIS;   
    uint256 public saldoAmil;  

    uint256 public constant PORSI_AMIL  = 125;
    uint256 public constant DENOMINATOR = 1000;

    // ====================================================================
    // ⭐ FULL WEB3: STATE MACHINE UNTUK PENYALURAN
    // ====================================================================
    enum StatusProgram { BelumCair, ProsesPelaksanaan, TelahTerkonfirmasi }

    struct DetailPenyaluran {
        uint256 nominal;
        string proposalIpfsHash; 
        string buktiIpfsHash;    
        StatusProgram status;    
    }

    mapping(uint256 => DetailPenyaluran) public dataPenyaluran;

    // --- Events ---
    event DepositZIS(
        address indexed muzakki,
        uint256 nominalTotal,
        uint256 nominalBersih,
        uint256 hakAmil,
        string  tipeDana,
        uint256 timestamp
    );

    // ⭐ EVENT DIUBAH: Tambah programId dan proposalHash
    event PencairanDana(
        address indexed admin,
        uint256 indexed programId, 
        uint256 nominal,
        address tujuan,
        string proposalHash,
        uint256 timestamp
    );

    // ⭐ EVENT BARU
    event KonfirmasiPenyaluranEvent(
        address indexed amil,
        uint256 indexed programId,
        string buktiHash,
        uint256 timestamp
    );

    event UpdateDompetPenyaluran(address indexed dompetLama, address indexed dompetBaru, uint256 timestamp);
    event UpdateAkunPenyaluran(address indexed akunLama, address indexed akunBaru, uint256 timestamp);

    // --- Modifiers ---
    modifier hanyaKeuangan() {
        require(msg.sender == adminKeuangan, "Akses Ditolak: Hanya Keuangan!");
        _;
    }

    modifier hanyaPemerintah() {
        require(msg.sender == akunPemerintah, "Akses Ditolak: Hanya Pemerintah!");
        _;
    }

    modifier hanyaPenyaluran() {
        require(msg.sender == akunPenyaluran, "Akses Ditolak: Hanya Tim Penyaluran!");
        _;
    }

    modifier saatAktif() {
        require(sistemAktif, "Sistem sedang dibekukan sementara");
        _;
    }
}