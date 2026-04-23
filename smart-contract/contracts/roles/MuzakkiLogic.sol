// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract MuzakkiLogic is ZakatBase {

    function bayarZIS(string memory _tipeDana) public payable saatAktif {
        require(msg.value > 0, "Nominal tidak boleh nol");

        // Hitung 12.5% hak amil
        uint256 hakAmil       = (msg.value * PORSI_AMIL) / DENOMINATOR;
        uint256 nominalBersih = msg.value - hakAmil;

        // Tambahkan ke masing-masing saldo
        saldoAmil += hakAmil;
        saldoZIS  += nominalBersih;

        emit DepositZIS(
            msg.sender,
            msg.value,      // nominalTotal
            nominalBersih,  // 87.5%
            hakAmil,        // 12.5%
            _tipeDana,
            block.timestamp
        );
    }
}