// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract KeuanganLogic is ZakatBase {
    
    // ✅ TAMBAHAN: Event untuk melacak perubahan secara transparan
    event UpdateDompetPenyaluran(address indexed dompetLama, address indexed dompetBaru, uint256 timestamp);
    event UpdateAkunPenyaluran(address indexed akunLama, address indexed akunBaru, uint256 timestamp);

    // Update Brankas Pasif (Hanya menerima uang)
    function updateDompetPenyaluran(address payable _dompetBaru) public hanyaPemerintah {
        require(_dompetBaru != address(0), "Alamat tidak boleh kosong (0x0)");
        require(_dompetBaru != dompetPenyaluran, "Alamat dompet sudah digunakan");
        
        address dompetLama = dompetPenyaluran;
        dompetPenyaluran = _dompetBaru;
        
        emit UpdateDompetPenyaluran(dompetLama, _dompetBaru, block.timestamp);
    }

    // Update Akun Login Penyaluran (Akses Aplikasi)
    function updateAkunPenyaluran(address _akunBaru) public hanyaPemerintah {
        require(_akunBaru != address(0), "Alamat tidak boleh kosong");
        require(_akunBaru != akunPenyaluran, "Akun sudah digunakan");

        address akunLama = akunPenyaluran;
        akunPenyaluran = _akunBaru;

        emit UpdateAkunPenyaluran(akunLama, _akunBaru, block.timestamp);
    }

    function cairkanDana(uint256 _nominal) public hanyaKeuangan saatAktif {
        require(address(this).balance >= _nominal, "Saldo tidak cukup");
        
        // ✅ PERUBAHAN: Dana otomatis dikirim ke dompet BRANKAS, bukan ke akun login
        dompetPenyaluran.transfer(_nominal);
        
        emit PencairanDana(
            msg.sender,
            _nominal,
            dompetPenyaluran,
            block.timestamp
        );
    }
}