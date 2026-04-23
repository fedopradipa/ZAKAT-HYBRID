// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract KeuanganLogic is ZakatBase {

    // TIDAK DIUBAH
    function updateDompetPenyaluran(address payable _dompetBaru) public hanyaPemerintah {
        require(_dompetBaru != address(0), "Alamat tidak boleh kosong (0x0)");
        require(_dompetBaru != dompetPenyaluran, "Alamat dompet sudah digunakan");
        address dompetLama = dompetPenyaluran;
        dompetPenyaluran = _dompetBaru;
        emit UpdateDompetPenyaluran(dompetLama, _dompetBaru, block.timestamp);
    }

    // TIDAK DIUBAH
    function updateAkunPenyaluran(address _akunBaru) public hanyaPemerintah {
        require(_akunBaru != address(0), "Alamat tidak boleh kosong");
        require(_akunBaru != akunPenyaluran, "Akun sudah digunakan");
        address akunLama = akunPenyaluran;
        akunPenyaluran = _akunBaru;
        emit UpdateAkunPenyaluran(akunLama, _akunBaru, block.timestamp);
    }

    function cairkanDana(uint256 _nominal) public hanyaKeuangan saatAktif {
        require(_nominal > 0, "Nominal tidak boleh nol");
        // ── DIUBAH: cek dari saldoZIS, bukan address(this).balance ──
        require(saldoZIS >= _nominal, "Saldo ZIS tidak cukup");

        // Kurangi saldoZIS dulu (Checks-Effects-Interactions pattern)
        saldoZIS -= _nominal;

        dompetPenyaluran.transfer(_nominal);

        emit PencairanDana(
            msg.sender,
            _nominal,
            dompetPenyaluran,
            block.timestamp
        );
    }
}