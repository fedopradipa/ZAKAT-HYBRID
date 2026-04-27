// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract KeuanganLogic is ZakatBase {

    function updateDompetPenyaluran(address payable _dompetBaru) public hanyaPemerintah {
        require(_dompetBaru != address(0), "Alamat tidak boleh kosong (0x0)");
        require(_dompetBaru != dompetPenyaluran, "Alamat dompet sudah digunakan");
        address dompetLama = dompetPenyaluran;
        dompetPenyaluran = _dompetBaru;
        emit UpdateDompetPenyaluran(dompetLama, _dompetBaru, block.timestamp);
    }

    function updateAkunPenyaluran(address _akunBaru) public hanyaPemerintah {
        require(_akunBaru != address(0), "Alamat tidak boleh kosong");
        require(_akunBaru != akunPenyaluran, "Akun sudah digunakan");
        address akunLama = akunPenyaluran;
        akunPenyaluran = _akunBaru;
        emit UpdateAkunPenyaluran(akunLama, _akunBaru, block.timestamp);
    }

    // ⭐ WEB3: Fungsi cairkanDana sekarang punya 3 Parameter!
    function cairkanDana(uint256 _nominal, uint256 _programId, string memory _proposalHash) public hanyaKeuangan saatAktif {
        require(_nominal > 0, "Nominal tidak boleh nol");
        require(bytes(_proposalHash).length > 0, "Hash proposal tidak boleh kosong");
        
        require(dataPenyaluran[_programId].status == StatusProgram.BelumCair, "Dana program ini sudah pernah dicairkan");
        require(saldoZIS >= _nominal, "Saldo ZIS tidak cukup");

        saldoZIS -= _nominal;

        // Merekam Realita ke Blockchain
        dataPenyaluran[_programId] = DetailPenyaluran({
            nominal: _nominal,
            proposalIpfsHash: _proposalHash,
            buktiIpfsHash: "", 
            status: StatusProgram.ProsesPelaksanaan
        });

        dompetPenyaluran.transfer(_nominal);

        emit PencairanDana(
            msg.sender,
            _programId,
            _nominal,
            dompetPenyaluran,
            _proposalHash,
            block.timestamp
        );
    }
}