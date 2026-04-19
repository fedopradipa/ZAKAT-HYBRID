// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "./roles/MuzakkiLogic.sol";
import "./roles/KeuanganLogic.sol";
import "./roles/PenyaluranLogic.sol";

contract PengelolaanZakat is MuzakkiLogic, KeuanganLogic, PenyaluranLogic {
    
    // ✅ PERUBAHAN OPSI 2: Constructor menerima 4 parameter sekarang
    constructor(
        address _adminKeuangan,
        address _akunPemerintah,
        address _akunPenyaluran,
        address payable _dompetPenyaluran
    ) {
        adminKeuangan = _adminKeuangan;
        akunPemerintah = _akunPemerintah;
        akunPenyaluran = _akunPenyaluran;
        dompetPenyaluran = _dompetPenyaluran;
    }

    // Fungsi tambahan khusus Pemerintah untuk kontrol sistem
    function setStatusSistem(bool _status) public hanyaPemerintah {
        sistemAktif = _status;
    }
}