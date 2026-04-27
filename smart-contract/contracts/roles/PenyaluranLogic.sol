// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract PenyaluranLogic is ZakatBase {
    function getSaldoBrankas() public view returns (uint256) {
        return address(this).balance;
    }

    // ⭐ WEB3: Fungsi untuk mengunci foto bukti setelah acara selesai
    function konfirmasiPenyaluranData(uint256 _programId, string memory _buktiHash) public hanyaPenyaluran saatAktif {
        require(bytes(_buktiHash).length > 0, "Hash bukti tidak boleh kosong");
        
        require(dataPenyaluran[_programId].status == StatusProgram.ProsesPelaksanaan, "Dana belum cair atau program sudah terkonfirmasi");
        
        dataPenyaluran[_programId].status = StatusProgram.TelahTerkonfirmasi;
        dataPenyaluran[_programId].buktiIpfsHash = _buktiHash;

        emit KonfirmasiPenyaluranEvent(
            msg.sender,
            _programId,
            _buktiHash,
            block.timestamp
        );
    }
}